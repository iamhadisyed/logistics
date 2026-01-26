<?php
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../includes/3rdparty/phpexcel/PHPExcel.php");
//require_once("../includes/mapping/glorderpdf8.class.php");
include_classes([
    'pdfmerger',
    'baglabel.class',
    'ocbaglabel.class'
],'labels');
include_classes([
    'tcpdf'
],'3rdparty/tcpdf');
include_classes([
    'PHPExcel'
],'3rdparty/phpexcel');
include_classes([
    'country.class',
    'flightinfo.class',
    'flightinfofilter.class',
    'mawb.class',
    'mawbfilter.class',
    'bagging.class',
    'baggingfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'mawbparcelmapping.class',
    'mawbparcelmappingfilter.class',
    'services.class',
    'flightmapping.class',
    'flightmappingfilter.class',
    'parcelbaggingmapping.class',
    'parcelbaggingmappingfilter.class',
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'currency.class',
    'countryfilter.class',
    'flight.class',
    'flightfilter.class',
    'sea.class',
    'seafilter.class',
    'truck.class',
    'truckfilter.class',
    'baggingservicesmapping.class',
    'baggingservicesmappingfilter.class',
    'warehouse.class',
    'carrier.class'
]);


class Page extends BasePage {

    /*     * *
     * Controller logic
     */
    public $userData = null;
    private $mawbTypeArray = array (''=>'Smart Track','CpostIps'=>'C Post Ips');
    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Country Bagging"
        );
        $user = SessionManager::getUser();
        $this->userData = $user;
        /*
         * DataTable handlings
         */
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "bag_switch") {
            $returnData = [];
            $isFixed = "";
            $bagFilter = "";
            $bagType = $this->form_vars['bag_type'];
            $baggingFilter = New BaggingFilter();
            $baggingFilter->addFieldFilter("    user_id",$this->userData->getId());
            $baggingFilter->addFieldFilter("    is_closed",'0');
            $baggingFilter->addFieldFilter("    is_country_bagging",'y');
            $baggingFilter->AddOrderBy("    id",false);
            $baggingObj = $baggingFilter->getColumnList("*");
            if(count($baggingObj) > 0){
                $isFixed = $baggingObj[0]->getIsFixed();
                $bagFilter = $baggingObj[0]->getBagFilter();
            }
            $josnRtn = json_decode($bagFilter,true);
            $returnData['is_fixed'] = $isFixed;
            $returnData['country_wise_bag'] = $josnRtn['country_wise_bag'];
            $returnData['service_wise_bag'] = $josnRtn['service_wise_bag'];
            $returnData['warehouse_wise_bag'] = $josnRtn['warehouse_wise_bag'];
            $returnData['destination_country_id'] = $josnRtn['destination_country_id'];
            $returnData['destination_warehouse_id'] = $josnRtn['destination_warehouse_id'];
            echo json_encode($returnData);
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "add_tracking") {
            /*
             * Check if bag is open
             */
            $bagFilter = [];
            $bagType = $this->form_vars['bag_type'];
            $countryWiseBag = $this->form_vars['country_wise_bag'];
            $serviceWiseBag = $this->form_vars['service_wise_bag'];
            $warehouseWiseBag = $this->form_vars['warehouse_wise_bag'];
            $destinationCountryId = $this->form_vars['destination_country_id'];
            $destinationWarehouseId = $this->form_vars['destination_warehouse_id'];
            if($bagType == "on"){
                $bagType = 'fixed';
            }else{
                $bagType = 'random';
            }
            $bagFilter['country_wise_bag'] = $countryWiseBag;
            $bagFilter['service_wise_bag'] = $serviceWiseBag;
            $bagFilter['warehouse_wise_bag'] = $warehouseWiseBag;
            $bagFilter['destination_country_id'] = $destinationCountryId;
            $bagFilter['destination_warehouse_id'] = $destinationWarehouseId;
            $user = SessionManager::getUser();
            $sourceWarehouseId = $user->getWarehouseId();
            $trackingNumber = trim($this->form_vars['tracking_number']);
            $trackingNumber = ParseTrackingNumber::Parse($trackingNumber);
            $returnData = [];
            if (empty($trackingNumber)) {
                $returnData['status'] = "error";
                $returnData['message'] = "Enter Tracking Number";
            }
            // Check if any bag is open of this user then move with that bag
            $baggingFilter = New BaggingFilter();
            $baggingFilter->addFieldFilter("    user_id", $user->getId());
            $baggingFilter->addFieldFilter("    is_closed", '0');
            $baggingFilter->addFieldFilter("    is_country_bagging", 'y');
            $baggingObjNew =  $baggingFilter->getColumnList("*");
            $isLowValBag = "lv";
            if (empty($returnData)) {
                if($bagType == "fixed"){
                    $sourceCountryId = $user->getCountryId();
                    // Check if parcel exist/ Get parcel service
                $parcel = new ParcelFilter();
                $parcelObj = $parcel->getParcelSerivce($trackingNumber);
                if (count($parcelObj) > 0) {
                    $bagValue = getBagIdWithValue($parcelObj[0]->getItemvalue(), $parcelObj[0]->getChuteSorted(), $destinationCountryId);
                    $isLowValBag = 'lv';
                    if (!empty($bagValue['value'])) {
                        if ($bagValue['value'] >= $bagValue['bag_low_value'])
                            $isLowValBag = 'hv';
                        else
                            $isLowValBag = 'lv';
                    }
                    $conDestinationWarehouseId = "";
                    $consignmentObj = New Consignment($parcelObj[0]->getConsignmentId());
                    $conServiceId = $consignmentObj->getServiceId();
                    $conDestinationWarehouseId = $consignmentObj->getDestinationWarehouseId();
                    // Check the carreir of that service
                    $carrierId = "";
                    if($serviceWiseBag == 1){
                        $service = New Services($conServiceId);
                        $carrier = New Carrier($service->getCarrierId());
                        $carrierId = $carrier->getId();
                        $bagFilter['bag_carrier'] = $carrierId;
                    }
                    $bagFilter = json_encode($bagFilter);
                    if(count($baggingObjNew) > 0){
                        // We have already some type oif open bag
                        $bagIsFixed = $baggingObjNew[0]->getIsFixed();
                        if(!empty($bagIsFixed) && $bagIsFixed == "y" && $bagType == "random"){
                            // Show error that fixed type bag is already open of this user
                            $returnData['status'] = "error";
                            $returnData['message'] = "You have already fixed bag [".$baggingObjNew[0]->getBagnumber()."] opened, Please close that bag";
                            echo json_encode($returnData);
                            die;
                        }
                        if(!empty($countryWiseBag) && $countryWiseBag == '1' && $baggingObjNew[0]->getBagDestinationCountryId() != $destinationCountryId){
                            // Show error that fixed type bag is already open of this user
                            $returnData['status'] = "error";
                            $returnData['message'] = "Your bag [".$baggingObjNew[0]->getBagnumber()."] is fixed and have different country";
                            echo json_encode($returnData);
                            die;
                        }
                        if(!empty($warehouseWiseBag) && $warehouseWiseBag == '1'){
                            if($conDestinationWarehouseId != $destinationWarehouseId){
                                // Show error that fixed type bag is already open of this user
                                $warehouse = New Warehouse($baggingObjNew[0]->getBagDestinationWarehouseId());
                                $returnData['status'] = "error";
                                $returnData['message'] = "Your bag [".$baggingObjNew[0]->getBagnumber()."] is for [".$warehouse->getWarehousename()."]  and you have scanned parcel from different warehouse. Please remove this shipment.";
                                echo json_encode($returnData);
                                die;
                            }
                            if($baggingObjNew[0]->getBagDestinationWarehouseId() != $destinationWarehouseId){
                                $returnData['status'] = "error";
                                $returnData['message'] = "Your bag [".$baggingObjNew[0]->getBagnumber()."] belongs to [".$warehouse->getWarehousename()."] please close bag to add this parcel";
                                echo json_encode($returnData);
                                die;
                            }
                            
                        }
                        if(!empty($serviceWiseBag) && $serviceWiseBag == '1'){
                            $baggingServicesMappingFilter = New BaggingServicesMappingFilter();
                            $baggingServiceMappingList = $baggingServicesMappingFilter->getBagCarrier($baggingObjNew[0]->getId());
                            if(count($baggingServiceMappingList) > 1){
                                $returnData['status'] = "error";
                                $returnData['message'] = "Your bag [".$baggingObjNew[0]->getBagnumber()."] is not a fixed bag, scanned more than one carrier";
                                echo json_encode($returnData);
                                die;
                            }else if(count($baggingServiceMappingList) > 0 && $baggingServiceMappingList[0]->getServiceId() != $carrierId){

                                $returnData['status'] = "error";
                                $returnData['message'] = "Your bag [".$baggingObjNew[0]->getBagnumber()."] is fixed bag, and have different carrier";
                                echo json_encode($returnData);
                                die;
                            }
                        }
                        // Check if bag weight limit is okay?
                        $bagTotalWeight = 0;
                        $bagId = "";
                        $bagginfFilter = new BaggingFilter();
                        $bagginfWeightTotal = $bagginfFilter->getBagParcelWeightByBagId($baggingObjNew[0]->getId());
                        if (count($bagginfWeightTotal) > 0) {
                            $bagTotalWeight = ($parcelObj[0]->getWeight()) + ($bagginfWeightTotal[0]->getActualWeight());
                        }
                        $countryBagWeightLimit = CountryFilter::getCountryBagWeightLimit($destinationCountryId);
                        if ($bagTotalWeight <= $countryBagWeightLimit) {
                            $bagValue = getBagIdWithValue($parcelObj[0]->getItemvalue(), $parcelObj[0]->getChuteSorted(), $destinationCountryId);
                            $isLowValBag = 'lv';
                            if (!empty($bagValue['value'])) {
                                if ($bagValue['value'] >= $bagValue['bag_low_value'])
                                    $isLowValBag = 'hv';
                                else
                                    $isLowValBag = 'lv';
                            }
                            // Bag is open can add parcel into it
                            $returnData = addDataIntoMappingTable($baggingObjNew[0]->getId(),  $parcelObj[0]->getId(), $baggingObjNew[0]->getBagnumber(),$isLowValBag,$trackingNumber,$conServiceId);
                        } else {
                            // Bag is opened and weight limit is over
                            $returnData['status'] = "error";
                            $returnData['message'] = "Bag weight limit [".$countryBagWeightLimit."] exceeded. Please close the bag [".$baggingObjNew[0]->getBagnumber()."]";
                            echo json_encode($returnData);
                            die;
                        }
                    }else{
                        // Create a new bag no previous bag is opened
                        if(!empty($warehouseWiseBag) && $warehouseWiseBag == '1'){
                            if($conDestinationWarehouseId != $destinationWarehouseId){
                                // Show error that fixed type bag is already open of this user
                                $returnData['status'] = "error";
                                $returnData['message'] = "Your Parcel does not belong to selected warehouse. Please select correct warehouse and scan parcel.";
                                echo json_encode($returnData);
                                die;
                            }
                        }
                        $bagdata = createBagWithServiceId($conServiceId, $sourceCountryId, $destinationCountryId,$isLowValBag,$sourceWarehouseId,"y",$carrierId,$bagFilter,$destinationWarehouseId);
                        $returnDataJson = json_decode($bagdata, true);
                        $returnData = addDataIntoMappingTable($returnDataJson['bag_id'],  $parcelObj[0]->getId(), $returnDataJson['bag_number'],$isLowValBag,$trackingNumber,$conServiceId);
                        echo json_encode($returnData);
                        die;
                    }
                }
                else {
                    // Parcel not found or service not found
                    $returnData['status'] = "error";
                    $returnData['message'] = "tracking not found";
                }
                }else{
                    $sourceCountryId = $user->getCountryId();
                    // Check if parcel exsist/ Get parcel service
                    $parcel = new ParcelFilter();
                    $parcelObj = $parcel->getParcelSerivce($trackingNumber);
                    if (count($parcelObj) > 0) {
                        if(count($baggingObjNew) > 0){
                            $bagIsFixed = $baggingObjNew[0]->getisFixed();
                            if(!empty($bagIsFixed) && $bagIsFixed == "n" && $bagType == "fixed"){
                                // Show error that fixed type bag is already open of this user
                                $returnData['status'] = "error";
                                $returnData['message'] = "You have already random bag [".$baggingObjNew[0]->getBagnumber()."] opened, Please close that bag";
                                echo json_encode($returnData);
                                die;
                            }
                        }
                        // Check if parcel is already added into bag
//                    $parcelBaggingMappingFilter = New ParcelBaggingMappingFilter();
//                    $parcelBaggingMappingFilter->addFieldFilter("    parcel_id",$parcelObj[0]->getId());
//                    $parcelBaggingMappingFilter->getColumnList("*");


                        $consignmentObj = New Consignment($parcelObj[0]->getConsignmentId());
                        $conServiceId = $consignmentObj->getServiceId();
                        $conDestinationWarehouseId = $consignmentObj->getDestinationWarehouseId();
                        // Check the carreir of that service
                        if($serviceWiseBag == 1){
                            $carrier = New Carrier($conServiceId);
                        }
                        $destinationCountryId = $consignmentObj->getCountryId();
                        $sourceWarehouseId = $user->getWarehouseId();

                        $bagging = new BaggingFilter();
                        $baggingObj = $bagging->getBagData($sourceCountryId,$destinationCountryId,$sourceWarehouseId,$user->getUserAccountId());
//                            $parcelBaggingMappingFilter = New ParcelBaggingMappingFilter();
//                            $parcelBaggingMappingFilter->addFieldFilter("    parcel_id",$parcelObj[0]->getId());
//                            $parcelBaggingMappingFilter->getColumnList("*");
                        // Check if parcel is already added into bag
//                            $mawbParcelMappingFilter = new MawbParcelMappingFilter();
//                            $mawbParcelMappingFilter->addFieldFilter("    parcel_id", $parcelObj[0]->getId());
//                            $mawbParcelMappingFilter->addFieldFilter("    wharehouse_id", $user->getWarehouseId());
//                            $mawbParcelBagFoundObj = $mawbParcelMappingFilter->getColumnList("*");
//                            if(count($mawbParcelBagFoundObj) > 0){
//                                $returnData['status'] = "error";
//                                $returnData['message'] = "Parcel is already added into bag [".$baggingNew->getBagnumber()."]";
//                                echo json_encode($returnData);
//                                die;
//                            }
                        // Check if same service open bag exsit and weight limit is less then country weight limit
//                            $bagging = new BaggingFilter();
//                            $baggingObj = $bagging->getServiceBag($parcelObj[0]->getQty(),$mawbId,$isLowValBag);
                        if (count($baggingObj) > 0) {
                            $baggingServicesData = [];
                            $bagTotalWeight = 0;
                            $bagId = "";
                            $bagginfFilter = new BaggingFilter();
                            $bagginfWeightTotal = $bagginfFilter->getBagParcelWeightByBagId($baggingObj[0]->getId());
                            if (count($bagginfWeightTotal) > 0) {
                                $bagTotalWeight = ($parcelObj[0]->getWeight()) + ($bagginfWeightTotal[0]->getActualWeight());
                            }
                            $countryBagWeightLimit = CountryFilter::getCountryBagWeightLimit($destinationCountryId);
                            if ($bagTotalWeight <= $countryBagWeightLimit) {
                                // Bag is open can add parcel into it
                                $returnData = addDataIntoMappingTable($baggingObj[0]->getId(),  $parcelObj[0]->getId(), $baggingObj[0]->getBagnumber(),$baggingObj[0]->getBagValue(),$trackingNumber,$conServiceId);
                            } else {
                                // Bag is opened and weight limit is over
                                $returnData['status'] = "error";
                                $returnData['message'] = "Bag weight limit [".$countryBagWeightLimit."] exceeded. Please close the bag [".$baggingObj[0]->getBagnumber()."]";
                            }
                        } else {
                            $bagFilter = json_encode($bagFilter);
                            // Now bag is opened with this service
                            $bagdata = createBagWithServiceId($conServiceId, $sourceCountryId, $destinationCountryId,$isLowValBag,$sourceWarehouseId,"n","",$bagFilter);
                            $returnDataJson = json_decode($bagdata, true);
                            $returnData = addDataIntoMappingTable($returnDataJson['bag_id'],  $parcelObj[0]->getId(), $returnDataJson['bag_number'],$isLowValBag,$trackingNumber,$conServiceId);
                        }
                    }
                    else {
                        // Parcel not found or service not found
                        $returnData['status'] = "error";
                        $returnData['message'] = "tracking not found";
                    }
                }
            }
            echo json_encode($returnData);
            die;
        }
        /// Mariya Task start
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "get_flight_mawb_stats_action") {
            $bagType = $this->form_vars['bag_type'];
            $countryWiseBag = $this->form_vars['country_wise_bag'];
            $serviceWiseBag = $this->form_vars['service_wise_bag'];
            $warehouseWiseBag = $this->form_vars['warehouse_wise_bag'];
            $destinationCountryId = $this->form_vars['destination_country_id'];
            $destinationWarehouseId = $this->form_vars['destination_warehouse_id'];
            if($bagType == "on"){
                // fixed
                $bagType = "y";
            }else{
                // Random
                $bagType = "n";
            }
            // Get those bags data which is not affiliated to any flight and mawb
            $user = SessionManager::getUser();
            $baggingFilter = New BaggingFilter();
            $baggingData = $baggingFilter->getOpenBagData($user->getWarehouseId(),$user->getId(),$bagType);
                $html = "";
                $dataArr = [];
            if (count($baggingData) > 0) {
                foreach ($baggingData as $bagData) {
                    $warehouseName = "";
                    $baggingObj = BaggingFilter::getParcelTotalFromBagId($bagData->getId());
//                    if($bagType == "random"){
                        $baggingParcelCount = 0;
                        if (count($baggingObj) > 0) {
                            $baggingParcelCount = $baggingObj[0]->getId();
                        }
                        $bagFilterJson = json_decode($bagData->getBagFilter(),true);
                        $warehouose = New Warehouse($bagData->getBagDestinationWarehouseId());
                        if(isset($bagFilterJson['warehouse_wise_bag']) && $bagFilterJson['warehouse_wise_bag'] == '1' && !empty($warehouose->getWarehouseName()) && empty($warehouseName)){
                            $warehouseName = ' [ '.$warehouose->getWarehouseName().' ]';
                        }
                        $dataArr[$bagData->getCountryName()][] = ['bag_id'=>$bagData->getId(),'is_closed'=>$bagData->getIsClosed(),"bag_label"=>$bagData->getBagLabel(),"bag_number"=>$bagData->getBagNumber(),"bag_weight"=>$bagData->getWeight(),"bag_manifest"=>$bagData->getBagManifest(),"bag_parcel_count"=>$baggingParcelCount, "bag_warehouse"=>$warehouseName];
//                    }else{
//                        // Check if 
//                        $baggingParcelCount = 0;
//                        if (count($baggingObj) > 0) {
//                            $baggingParcelCount = $baggingObj[0]->getId();
//                        }
//                        $dataArr[$bagData->getCountryName()][] = ['bag_id'=>$bagData->getId(),'is_closed'=>$bagData->getIsClosed(),"bag_label"=>$bagData->getBagLabel(),"bag_number"=>$bagData->getBagNumber(),"bag_weight"=>$bagData->getWeight(),"bag_manifest"=>$bagData->getBagManifest(),"bag_parcel_count"=>$baggingParcelCount];
//                    }
                }
            }
            if (count($dataArr) > 0) {
                foreach ($dataArr as $country => $bagData) {
                    $html .= '<div class="panel panel-default">
                                        <!-- Default panel contents -->
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Country:&nbsp;'.$country.'</h3>
                                        </div>
                                        <!-- Table -->
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center"> # </th>
                                                    <th> Bag No. </th>
                                                    <th width="20%" class="text-center"> Parcels </th>
                                                    <th width="20%" class="text-center"> Status </th>
                                                    <th width="20%" class="text-center"> Action </th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                        $i = 1;
                        foreach ($bagData as $singleBagData) {
                            if ($singleBagData['is_closed'] == 0) {
                                $labelClass = "label-success";
                                $label = "Open";
                                $button = "<a href='javascript:void(0);'  data-bag_id='" . $singleBagData['bag_id'] . "' class='btn btn-primary btn-sm close_bag'>Close</a>";
                            } else {
                                $closedBag++;
                                $labelClass = "label-danger";
                                $label = "Closed";
                                $button = '';
                                $button .= '<a href="javascript:void(0);" data-bag_id="' . $singleBagData['bag_id'] . '" class="margin-right-10 reopen_bag" data-toggle="tooltip" title="Re-Open"><i class="fa fa-retweet"></i></a>';
                                $button .= '<a href="' . $singleBagData['bag_label'] . '"  class="margin-right-10" data-toggle="tooltip" target="_blank" title="Download Label"><i class="fa fa-file-pdf-o"></i></a>';
                                $button .= '<a href="'.$singleBagData['bag_manifest'].'" class="margin-right-10" data-toggle="tooltip" title="Download Manifest"><i class="fa fa-file-excel-o "></i></a>';
                            }
                            $bagValueCls = 'label-success';
                            $html .= '
                                    <tr>
                                        <td class="text-center"> ' . $i . ' </td>
                                        <td>' . $singleBagData['bag_number'] . " - " . $singleBagData['bag_warehouse'] . ' </td>
                                        <td style="cursor: pointer;" class="text-center" onclick="get_parcel_details(\'' . $singleBagData['bag_id'] . '\')">' . $singleBagData['bag_weight'] . ' Kg <br /><small><i><strong>Parcels:</strong> ' . $singleBagData['bag_parcel_count'] . '</i></small></td>
                                        <td class="text-center"> <span class="label label-sm ' . $labelClass . '"> ' . $label . ' </span> </td>
                                        <td class="text-center">' . $button . '</td>
                                    </tr>';


                            $i++;
                        }
                    $html .= '</tbody></table></div>';
                }
            }
//                echo "<pre>";
                $returnData['status'] = "success";
                $returnData['html'] = $html;
                echo json_encode($returnData);
                die;
        }

        /// Mariya task end
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "close_bag") {
            $user = SessionManager::getUser();
            $returnData = [];
            $returnData['status'] = "error";
            $returnData['message'] = "Bag can not be closed";
            $bagId = $this->form_vars['bag_id'];
            $label = "";
            $bagNumber = '';
            if ($bagId > 0) {
                // Get all services from bagging service mapping
                $serviceArr = [];
                $baggingServicesMappingFilter = New BaggingServicesMappingFilter();
                $baggingServicesMappingFilter->addFieldFilter("   bag_id",$bagId);
                $baggingServicesMappingObj = $baggingServicesMappingFilter->getColumnList("*");
                if(count($baggingServicesMappingObj) > 0){
                    foreach ($baggingServicesMappingObj as $baggingServicesMappingData) {
                        $serviceArr[] = $baggingServicesMappingData->getServiceId();
                    }
                }
                $serviceArr = array_unique($serviceArr);
                $parcelTrackingArr = [];
                $parcelBaggingMappingFilter = new ParcelBaggingMappingFilter();
                $parcelBaggingMappingFilter->addFieldFilter("     bag_id",$bagId);
                $parcelBaggingMappingObj = $parcelBaggingMappingFilter->getColumnList("*");
                if(count($parcelBaggingMappingObj) > 0){
                    foreach ($parcelBaggingMappingObj as $parcelBaggingMappingData) {
                        $parcel = New Parcel($parcelBaggingMappingData->getParcelId());
                        $parcelTrackingArr[] = $parcel->getTrackingNumber();
                    }
                }
                $parcelTrackingArr = array_unique($parcelTrackingArr);
                $bagging = new Bagging($bagId);
                $bagging->setIsClosed(1);
                $bagging->setClosedBy($user->getId());
                $bagging->setClosedDate(time());

                if(count($serviceArr) > 1) {
                    $bagging->setBagType("MIX");
                }else{
                    $bagging->setBagType("NORMAL");
                }
                $bagging->save();
                if(count($serviceArr) == 1) {
                    $serviceObj = new Services($serviceArr[0]);
                    $className = trim($serviceObj->getLabelClassName());
                    $classFound = false;
                    if (trim($className) != '') {
                        $file = "../includes/labels/" . strtolower($className) . ".class.php";
                        if (is_file($file)) {
                            require_once($file);

                            if(method_exists($className, "bagLabel"))
                                $classFound = true;
                        }
                    }
                    if ($classFound) {
                        $classObject = new $className();
                        $bagLabelResponse = $classObject->bagLabel($parcelTrackingArr);
                        if ($bagLabelResponse["STATUS"] == "SUCCESS") {
                            $bagging->setBagLabel($bagLabelResponse["LABEL"]);
                            $bagging->setBagNumber($bagLabelResponse["BAG_NUMBER"]);
                            $bagging->save();
                        }
                        $label = $bagLabelResponse["LABEL"];
                    } else {
                        $label = generateBagLabel($bagId);
                    }
                }else{
                    $label = generateBagLabel($bagId);
                }
                $bagManifestExcel = getBagManifestExcel($bagId);
                $returnData['status'] = "success";
                $returnData['label'] = $label;
                $returnData['message'] = "Bag [".$bagging->getBagnumber()."] is closed successfully";
            }
            echo json_encode($returnData);
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "open_bag") {
            $user = SessionManager::getUser();
            $bagId = $this->form_vars['bag_id'];
            $returnData = [];
            $returnData['status'] = "error";
            $returnData['message'] = "Bag can not be re-open";
            if ($bagId > 0) {
                $bagging = new Bagging($bagId);
                $bagging->setIsClosed(0);
                $bagging->setReopenBy($user->getId());
                $bagging->setReopenDate(time());
                $bagging->save();
                $returnData['status'] = "success";
                $returnData['message'] = "Bag is re-open successfully";
            }
            echo json_encode($returnData);
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "close_all_bag"){
            $pdfMerger = new PDFMerger();
            $errorBagNumbers = [];
            $mawbId = $this->form_vars['mawb_id'];
            $returnData['status'] = "error";
            $returnData['message'] = "Bag can not be closed";
            if($mawbId > 0){
                // Get all bags of mawb
                $mawbParcelMappingFilter = new MawbParcelMappingFilter();
                $mawbParcelMappingFilter->addFieldFilter("    mawb_id", $mawbId);
                $mawbParcelMappingObj = $mawbParcelMappingFilter->getColumnList("bag_id");
                if(count($mawbParcelMappingObj) > 0){
                    foreach ($mawbParcelMappingObj as $mawbParcelMappingArr) {
                        $bagNumber = '';

                        $bagId = $mawbParcelMappingArr->getBagId();
                        $bagging = new Bagging($bagId);
                        if($bagging->getIsClosed() == 0){
                            $mawb = new Mawb($mawbId);
                            $mawbClass = $mawb->getMawbClass();
                            if(isset($mawbClass) && !empty($mawbClass)) {
                                include_classes([
                                    strtolower($mawbClass) . '.class'
                                ], 'labels');
                                $consignmentList = new ParcelBaggingMappingFilter();
                                $consignmentList->addJoin('parcel p', 'p.id', 'pbm.parcel_id', 'INNER JOIN');
                                $consignmentList->addJoin('consignment c', 'c.id', 'p.consignment_id', 'INNER JOIN');
                                $consignmentList->addFieldFilter('     pbm.bag_id', $bagId);
                                $consignmentList = $consignmentList->getList('p.weight, p.tracking_number, c.sender_country_id, c.sender_postcode, c.sender_city, c.sender_address_line_1, c.sender_country_id, c.country_id, c.currency, c.value, c.contact, c.address_line_1, c.city, c.postcode, c.sender_name, c.sender_telephone, c.telephone');
                                $serivceClass = new $mawbClass();
                                // Pass Consignment List
                                $serviceResponse = $serivceClass->addBagItems($consignmentList, $mawb->getMawbNumber());
                                if ($serviceResponse['STATUS'] == 'SUCCESS') {
                                    $bagNumber = $serviceResponse['BAG_NUMBER'];
                                } else {
                                    $errorBagNumbers[] = $bagging->getBagNumber();
                                }
                            }
                            if(!empty($bagNumber)) {
                                $bagging->setBagNumber($bagNumber);
                            }
                            $bagging->setIsClosed(1);
                            $bagging->setClosedBy($user->getId());
                            $bagging->setClosedDate(time());
                            $bagging->save();
                            $label = generateBagLabel($bagId);
                            $bagManifestExcel = getBagManifestExcel($bagId);
                        }else{
                            if(!empty($bagging->getBagLabel())){
                                $labelArr = explode("_assets",$bagging->getBagLabel());
                                $fileDirectoryPath = $_SERVER['DOCUMENT_ROOT']."/_assets".$labelArr[1];
                                if (file_exists($fileDirectoryPath)) {
                                    $label = $bagging->getBagLabel();
                                } else {
                                    $label = generateBagLabel($bagId);
                                }
                            }else {
                                $label = generateBagLabel($bagId);
                            }
                        }
                        $labelArr = explode("_assets",$label);
                        $pdfMerger->addPDF($_SERVER['DOCUMENT_ROOT']."/_assets".$labelArr[1], 'all');
                    }
                    $folderPath = '../_assets/export_manifest/';
                    if (!file_exists($folderPath)) {
                        mkdir($folderPath, 0777, true);
                    }
                    $fileName = "manifest_".time()."_".$mawbId.'.pdf';
                    $outFile = $folderPath.$fileName;
                    $filePathDb = SETTING_MAIN_ASSETS."export_manifest/".$fileName;
                    $pdfMerger->merge('file', $outFile);
                    $mawbObj = new Mawb($mawbId);
                    $mawbObj->setManifestLabel($filePathDb);
                    $mawbObj->save();
                    $returnData['status'] = "success";
                    $returnData['message'] = "All bags are closed successfully. ";
                    if(!empty($errorBagNumbers)) {
                        $returnData['message'] .= '<br> Following Bags Data can not be added to service. <br>' . implode('<br>', $errorBagNumbers);
                    }
                }
            }
            echo json_encode($returnData);
            die;
        }
        if (isset($_GET['action']) && $_GET['action'] == "parcel_detail_ajax") {
            $parcelBaggingMappingFilter = new ParcelBaggingMappingFilter();
            $parcelBaggingMappingFilter->addJoin("parcel p", "p.id", "pbm.parcel_id");
            $parcelBaggingMappingFilter->addJoin("bagging b", "b.id", "pbm.bag_id");

            $bagId = $this->form_vars['bag_filter'];
            if(!empty($bagId)) {
                $parcelBaggingMappingFilter->addFieldFilter('    pbm.bag_id', $bagId);
            }
            $parcelBaggingMappingFilter->addGroupBy("pbm.parcel_id");
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $trackingNumber = $this->form_vars['tracking_number'];
                if (!empty($trackingNumber))
                    $parcelBaggingMappingFilter->addFieldFilter('    p.tracking_number', $trackingNumber);

                $weight = $this->form_vars['weight'];
                if (!empty($weight))
                    $parcelBaggingMappingFilter->addFieldLikeFilter('    p.weight', $weight);

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

                if($dataTableColumnName == "tracking_number") {
                    $dataTableColumnName = "p.tracking_number";
                }
                $parcelBaggingMappingFilter->AddOrderBy(strtolower($dataTableColumnName), $orderFalse);
            }
            /*
             * Pagination Logic Implemented
             *
             */
            $iTotalRecords = $parcelBaggingMappingFilter->getParcelPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $parcelBaggingMappingFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $parcelBaggingMappingFilter->setOffset($iDisplayStart);
            $parcelBaggingMappingFilterobjs = $parcelBaggingMappingFilter->getPagingList("p.`id`,p.`tracking_number`,p.`length`,p.`width`,p.`height`,p.`weight`,p.`parcel_status_code`,b.id AS bag_id");
            $setDataArr = array();
            foreach ($parcelBaggingMappingFilterobjs as $key => $parcelBaggingMappingFilterobj) {
                $shipment_status = Consignment::getShipnmentStatus($parcelBaggingMappingFilterobj->getParcelStatusCode());
                $currentArr = array();
                $currentArr['tracking_number'] = $parcelBaggingMappingFilterobj->getTrackingNumber();
                $currentArr['dims'] = $parcelBaggingMappingFilterobj->getLength() . " x " . $parcelBaggingMappingFilterobj->getWidth() . " x " . $parcelBaggingMappingFilterobj->getHeight();
                $currentArr['weight'] = $parcelBaggingMappingFilterobj->getWeight();
                $currentArr['status'] = $shipment_status;
                $currentArr['actions'] = '<a class="btn btn-xs btn-default red btn-outline center types_delete" title="Delete Parcel" href="javascript:;" onclick="delete_parcel(' . "'" . $parcelBaggingMappingFilterobj->getId() . "'" . ',' . "'" . $parcelBaggingMappingFilterobj->getBagId()  . "'" . ')"><span class="fa fa-trash"></span></a>';
                $setDataArr [] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "save_bag_detail") {
            $bagId = $this->form_vars['bag_id'];
            $bagActualWeight = $this->form_vars['bag_actual_weight'];
            $bagLength = $this->form_vars['bag_length'];
            $bagWidth = $this->form_vars['bag_width'];
            $bagHeight = $this->form_vars['bag_height'];
            $bagging = New Bagging($bagId);
            $bagging->setActualWeight($bagActualWeight);
            $bagging->setLength($bagLength);
            $bagging->setWidth($bagWidth);
            $bagging->setHeight($bagHeight);
            $bagging->save();
            $output['status'] = "success";
            $output['message'] = "Bag details updated successfully";
            echo json_encode($output);
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "delete_parcel") {
            $output = [];
            $bagId = $this->form_vars['bag_id'];
            $parcelId = $this->form_vars['parcel_id'];
            if($bagId > 0 && $parcelId > 0){
                // Check if bag is closed
                $bagging = new Bagging($bagId);
                if($bagging->getIsClosed() == 1){
                    $output['status'] = "error";
                    $output['message'] = "Bag is closed. Cannot remove parcel";
                }else{
                    $parcelBaggingMappingFilter = new ParcelBaggingMappingFilter();
                    $parcelBaggingMappingFilter->addFieldFilter("      parcel_id", $parcelId);
                    $parcelBaggingMappingFilter->addFieldFilter("      bag_id", $bagId);
                    $parcelBaggingMappingFilter->delete_parcel_from_mapping();
                    $mawbParcelBaggingMappingFilter = new MawbParcelMappingFilter();
                    $mawbParcelBaggingMappingFilter->addFieldFilter("      parcel_id", $parcelId);
                    $mawbParcelBaggingMappingFilter->addFieldFilter("      bag_id", $bagId);
                    $mawbParcelBaggingMappingFilter->delete_parcel_from_mapping();
                    $parcelObj = new Parcel($parcelId);
                    $baggingObj = new Bagging($bagId);
                    $newBagWeight = $baggingObj->getWeight() - $parcelObj->getWeight();
                    $baggingObj->setWeight($newBagWeight);
                    $baggingObj->save();
                    // Remove bag if no parcel belong to it
                    $parcelBaggingMappingFilterobj = New ParcelBaggingMappingFilter();
                    $parcelBaggingMappingFilterobj->addFieldFilter("     parcel_id",$bagId);
                    $parcelBaggingMappingFilterObj = $parcelBaggingMappingFilterobj->getColumnList("*");
                    if(count($parcelBaggingMappingFilterObj) == 0){
                        $baggingObj->setIsClosed(1);
                        $baggingObj->setClosedBy($this->userData->getId());
                        $baggingObj->setClosedDate(time());
                        $baggingObj->save();
                    }

                    $output['status'] = "success";
                    $output['message'] = "Your parcel is deleted successfully";
                }
            }else{
                $output['status'] = "error";
                $output['message'] = "parcel or bag not found";
            }
            echo json_encode($output);
            die;
        }
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
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
        <style>
            .inputfield_custom_style{
                width:100% !important;
                height:100px !important;
                font-size:32px !important;
                background-color:#f5f5f5;
            }
        </style>
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                change_bag_type("ready");
                parcelDataTableFun.init();
                var flight_id = $("#flight").val();
                get_flight_mawb_stats();
                $(".reload").click(function () {
                    get_flight_mawb_stats();
                });
                $('body').on('change', '#mawb_class', function () {
                    var that = $(this);
                    if(that.val() != '') {
                        $('#mawb_number').attr('readonly', 'readonly');
                    } else {
                        $('#mawb_number').removeAttr('readonly');
                    }
                });
            });
            function checkKeyValue(e) {
                if (e.keyCode == 13) {
                    var tracking_number = $("#tracking_number").val();
                    var flight_id = $("#flight").val();
                    var mawb_id = $("#mawb").val();
                    var message = "";
                    var message_class = "success";
                    var country_wise_bag;
                    var service_wise_bag;
                    var warehouse_wise_bag;
                    var bag_type;
                    var destination_country_id;
                    var destination_warehouse_id;
                    var checkedBag = false;
                    var send_ajax = false;
                    bag_type = $.trim($("#bag_type:checked").val());
                    if(!$("#country_wise_bag").is(":checked") && bag_type == "on"){
                        $("#show_general_msg div.alert").html(" ");
                        $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                        $("#show_general_msg div.alert").html("Please check country");
                        $("#show_general_msg").show();
                        return false;
                    }
                    if($("#country_wise_bag").is(":checked")){
                        country_wise_bag = 1;
                        checkedBag = true;
                    }
                    if($("#service_wise_bag").is(":checked")){
                        service_wise_bag = 1;
                        checkedBag = true;
                    }
                    if($("#warehouse_wise_bag").is(":checked")){
                        warehouse_wise_bag = 1;
                        checkedBag = true;
                    }
                    destination_country_id = $.trim($("#destination_country_id").val());
                    destination_warehouse_id = $.trim($("#destination_warehouse_id").val());
                    $("#show_general_msg div.alert").html(" ");
                    $("#show_general_msg div.alert").removeClass('alert-success');
                    $("#show_general_msg div.alert").removeClass('alert-danger');
                    if (tracking_number == "") {
                        message = "Enter tracking number";
                        message_class = "danger";
                    }
                    if (message) {
                        $("#show_general_msg div.alert").addClass('alert-' + message_class);
                        $("#show_general_msg div.alert").html(message);
                        $("#show_general_msg").show();
                    } else {
                        send_ajax = true;
                        if(bag_type == "on" && checkedBag == false){
                            $("#show_general_msg div.alert").html(" ");
                            $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                            $("#show_general_msg div.alert").html("Please check country, service or warehouse");
                            $("#show_general_msg").show();
                            send_ajax = false;
                        }else if(bag_type == "on" && checkedBag == true){
                            if($("#country_wise_bag").is(":checked") && destination_country_id == ""){
                                $("#show_general_msg div.alert").html(" ");
                                $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                                $("#show_general_msg div.alert").html("Please select destination country");
                                $("#show_general_msg").show();
                                send_ajax = false;
                            }
                            if($("#warehouse_wise_bag").is(":checked") && destination_warehouse_id == ""){
                                $("#show_general_msg div.alert").html(" ");
                                $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                                $("#show_general_msg div.alert").html("Please select destination warehouse");
                                $("#show_general_msg").show();
                                send_ajax = false;
                            }
                        }
                        if(send_ajax){
                            $.ajax({
                                url: "country_bag.php", // point to server-side PHP script
                                dataType: 'html', // what to expect back from the PHP script, if anything
                                data: {
                                    action: 'add_tracking',
                                    flight_id: flight_id,
                                    mawb_id: mawb_id,
                                    tracking_number: tracking_number,
                                    country_wise_bag: country_wise_bag,
                                    service_wise_bag: service_wise_bag,
                                    warehouse_wise_bag: warehouse_wise_bag,
                                    bag_type: bag_type,
                                    destination_country_id: destination_country_id,
                                    destination_warehouse_id: destination_warehouse_id
                                },
                                type: 'post',
                                success: function (php_script_response) {
                                    $("#tracking_number").val('');
                                    php_script_response = $.parseJSON(php_script_response);
                                    get_flight_mawb_stats();
                                    $("#tracking_number").val("");
                                    if (php_script_response.status == "success") {
                                        $("#show_general_msg div.alert").html(" ");
                                        $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                                        $("#show_general_msg div.alert").html(php_script_response.message);
                                        $("#show_general_msg").show();
                                    } else {
                                        swal("Error!", php_script_response.message, "error");
                                    }
                                }
                            });
                        }
                    }
                }
            }
            $('#bag_type').on('switchChange.bootstrapSwitch', function (event, state) {
                change_bag_type("");
            });
            function change_bag_type(submitted_from){
                var state = $('#bag_type').bootstrapSwitch('state');
                var hide_div = false;
                var bag_type = 'random';
                if (state == true) {
                    hide_div = true;
                    bag_type = 'fixed';
                } else {
                    hide_div = false;
                }
                $.ajax({
                    url: "country_bag.php", // point to server-side PHP script
                    data: {
                        action: 'bag_switch',bag_type:bag_type
                    },
                    dataType: "json",
                    type: 'post',
                    success: function (php_script_response) {
                        if(php_script_response.is_fixed == "n" && bag_type == "fixed"){
                            // Show error that you are trying to switch bag to fixed while you have open random bag
                            $("#show_general_msg div.alert").html(" ");
                            $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                            $("#show_general_msg div.alert").html("You are trying to switch bag to fixed while you have open random bag");
                            $("#show_general_msg").show();
                            $('#bag_type').bootstrapSwitch('state', false);
                        }else if(php_script_response.is_fixed == "y" && bag_type == "random"){
                            // Show error that you are trying to switch bag to ramdom while you have open fixed bag
                            // Also set filter here so he can't change
                            if(submitted_from == "" || submitted_from != "ready") {
                                $("#show_general_msg div.alert").html(" ");
                                $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                                $("#show_general_msg div.alert").html("You are trying to switch bag to random while you have open fixed bag");
                                $("#show_general_msg").show();
                            }
                            $('#bag_type').bootstrapSwitch('state', true);
                        }else if(php_script_response.is_fixed == "y" && bag_type == "fixed"){
                            if(php_script_response.country_wise_bag !="" && php_script_response.country_wise_bag == '1'){
                                $('#country_wise_bag').iCheck('check');
                            }else{
                                $('#country_wise_bag').iCheck('uncheck');
                            }
                            if(php_script_response.service_wise_bag !="" && php_script_response.service_wise_bag == '1'){
                                $('#service_wise_bag').iCheck('check');
                            }else{
                                $('#service_wise_bag').iCheck('uncheck');
                            }
                            if(php_script_response.warehouse_wise_bag !="" && php_script_response.warehouse_wise_bag == '1'){
                                $('#warehouse_wise_bag').iCheck('check');
                            }else{
                                $('#warehouse_wise_bag').iCheck('uncheck');
                            }
                            if(php_script_response.destination_country_id !="" && php_script_response.destination_country_id > 0){
                                $('#destination_country_id').val(php_script_response.destination_country_id);
                                $('#destination_country_id').selectpicker('refresh');
                                $("#destination_country_id").change();
                            }
                            if(php_script_response.destination_warehouse_id !="" && php_script_response.destination_warehouse_id > 0){
                                $('#destination_warehouse_id').val(php_script_response.destination_warehouse_id);
                                $('#destination_warehouse_id').selectpicker('refresh');
                            }
                        }
                        $("#reload_id").click();
                    }
                });
                if(hide_div){
                    $("#fixed_div").show();
                }else{
                    $("#fixed_div").hide();
                }
            }
            $(document).on('click', '.close_bag', function () {
                var bag_id = $(this).data('bag_id');
                var mawb_id = $(this).data('mawb_id');
                var flight_id = $("#flight").val();
                var flightType = $('input[name="flight_type"]:checked').val();
                $("#bag_detail_bag_id").val(bag_id);
                $("#bag_detail_mawb_id").val(mawb_id);
                $("#bag_details").modal("show");
            });
            $(document).on('click', '#update_bag', function (){
                var bag_id = $("#bag_detail_bag_id").val();
                var mawb_id = $("#bag_detail_mawb_id").val();
                var bag_actual_weight = $("#bag_actual_weight").val();
                var bag_length = $("#bag_length").val();
                var bag_width = $("#bag_width").val();
                var bag_height = $("#bag_height").val();
                var flight_id = $("#flight").val();
                $.ajax({
                    url: "country_bag.php", // point to server-side PHP script
                    data: {
                        bag_id: bag_id,
                        bag_actual_weight: bag_actual_weight,
                        bag_length: bag_length,
                        bag_width: bag_width,
                        bag_height: bag_height,
                        action: 'save_bag_detail'
                    },
                    dataType: "json",
                    type: 'post',
                    success: function (php_script_response) {
                        if (php_script_response.status == "success") {
                            $("#bag_details").modal("hide");
                            $.ajax({
                                url: "country_bag.php", // point to server-side PHP script
                                data: {
                                    bag_id: bag_id,
                                    mawb_id: mawb_id,
                                    action: 'close_bag'
                                },
                                dataType: "json",
                                type: 'post',
                                success: function (php_script_response) {
                                    if (php_script_response.status == "success") {
                                        $("#show_general_msg div.alert").html(" ");
                                        $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                                        $("#show_general_msg div.alert").html(php_script_response.message);
                                        $("#show_general_msg").show();
                                        get_flight_mawb_stats();
                                        console.log(php_script_response.label);
                                        newwindow = window.open(php_script_response.label, 'Label', 'height=400,width=400');
                                        if (window.focus) {
                                            newwindow.focus()
                                        }
                                    } else {
                                        $("#show_general_msg div.alert").html(" ");
                                        $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                                        $("#show_general_msg div.alert").html(php_script_response.message);
                                        $("#show_general_msg").show();
                                    }
                                }
                            });
                        }
                    }
                });
            });
            $(document).on('click', '.reopen_bag', function () {
                var bag_id = $(this).data('bag_id');
                var flight_id = $("#flight").val();
                swal({
                        title: "Are you sure you want to re-open the bag",
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
                                url: "country_bag.php", // point to server-side PHP script
                                data: {
                                    bag_id: bag_id,
                                    action: 'open_bag'
                                },
                                dataType: "json",
                                type: 'post',
                                success: function (php_script_response) {
                                    if (php_script_response.status == "success") {
                                        $("#show_general_msg div.alert").html(" ");
                                        $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                                        $("#show_general_msg div.alert").html(php_script_response.message);
                                        $("#show_general_msg").show();
                                        get_flight_mawb_stats();
                                    } else {
                                        $("#show_general_msg div.alert").html(" ");
                                        $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                                        $("#show_general_msg div.alert").html(php_script_response.message);
                                        $("#show_general_msg").show();
                                    }
                                }
                            });
                        }
                    });
            });
            $(document).on('click', '.close_bags', function () {
                var mawbId = $(this).data("mawb_id");
                var flight_id = $("#flight").val();
                swal({
                        title: "Are you sure you want to close all bag",
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
                                type: "POST",
                                url: "mawb_flight_manage.php",
                                data: {action: "close_all_bag", mawb_id: mawbId},
                                dataType: "json",
                                success: function (data) {
                                    get_flight_mawb_stats();
                                    if (data.status == "success") {
                                        $("#show_general_msg div.alert").html(" ");
                                        $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                                        $("#show_general_msg div.alert").html(data.message);
                                        $("#show_general_msg").show();
                                    } else {
                                        $("#show_general_msg div.alert").html(" ");
                                        $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                                        $("#show_general_msg div.alert").html(data.message);
                                        $("#show_general_msg").show();
                                    }
                                },
                                error: function () {
                                    $("#show_general_msg div.alert").html(" ");
                                    $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                                    $("#show_general_msg div.alert").html("Sorry! There is some error.");
                                    $("#show_general_msg").show();
                                }
                            });
                        }
                    });
            });
            function get_flight_mawb_stats() {
                var country_wise_bag;
                var service_wise_bag;
                var warehouse_wise_bag;
                var bag_type;
                var destination_country_id;
                var destination_warehouse_id;
                if($("#country_wise_bag").is(":checked")){
                    country_wise_bag = 1;
                }
                if($("#service_wise_bag").is(":checked")){
                    service_wise_bag = 1;
                }
                if($("#warehouse_wise_bag").is(":checked")){
                    warehouse_wise_bag = 1;
                }
                bag_type = $.trim($("#bag_type:checked").val());
                destination_country_id = $.trim($("#destination_country_id").val());
                destination_warehouse_id = $.trim($("#destination_warehouse_id").val());

                $.ajax({
                    url: "country_bag.php", // point to server-side PHP script
                    data: {
                        action: 'get_flight_mawb_stats_action',country_wise_bag:country_wise_bag,service_wise_bag:service_wise_bag,warehouse_wise_bag:warehouse_wise_bag,bag_type:bag_type,destination_country_id:destination_country_id,destination_warehouse_id:destination_warehouse_id
                    },
                    dataType: "json",
                    type: 'post',
                    success: function (php_script_response) {
                        if (php_script_response.status == "success") {
                            $("#flight_info_stats").html(php_script_response.html);
                        } else {

                        }
                        $('[data-toggle="tooltip"]').tooltip();
                        //                            check_mawb_bag();
                    }
                });
            }
            var parcelGrid = null;
            var parcelDataTableFun = function () {
                var BaghandleDataTable = function () {
                    var bag_id = $('#bag_filter').val();
                    var bagdatatableurl = "country_bag.php?action=parcel_detail_ajax";
                    parcelGrid = new Datatable();
                    parcelGrid.init({
                        src: $("#parcel-Detail-table"),
                        onSuccess: function (grid) {
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
                                "url": bagdatatableurl, // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "tracking_number"},
                                {"data": "dims", "bSortable": false},
                                {"data": "weight"},
                                {"data": "status", "bSortable": false}
                            ]
                        }
                    });
                }
                return {
                    //main function to initiate the module
                    init: function () {
                        BaghandleDataTable();
                    }
                };
            }();
            function get_parcel_details(bag_id) {
                $('#bag_filter').val(bag_id);
                $('textarea.form-filter, select.form-filter, input.form-filter:not([type="radio"],[type="checkbox"])').each(function () {
                    parcelGrid.setAjaxParam($(this).attr("name"), $(this).val());
                });
                // get all checkboxes
                $('input.form-filter[type="checkbox"]:checked').each(function () {
                    parcelGrid.addAjaxParam($(this).attr("name"), $(this).val());
                });
                // get all radio buttons
                $('input.form-filter[type="radio"]:checked').each(function () {
                    parcelGrid.setAjaxParam($(this).attr("name"), $(this).val());
                });
                parcelGrid.submitFilter();
                $('#parcel_detail_model').modal('show');
            }
            function delete_parcel(parcel_id,bag_id) {
                var form_data = new FormData();
                form_data.append('parcel_id', parcel_id);
                form_data.append('bag_id', bag_id);
                form_data.append('action', 'delete_parcel');
                swal({
                        title: "Are you sure you want to remove parcel form bag",
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
                                url: 'country_bag.php',
                                cache: false,
                                contentType: false,
                                processData: false,
                                data: form_data,
                                type: 'post',
                                dataType: 'json',
                                success: function (data) {
                                    if(data.status == "success") {
                                        parcelGrid.getDataTable().ajax.reload();
                                        var flight_id = $("#flight").val();
                                        get_flight_mawb_stats(flight_id);
                                        swal("Success!", data.message, "success");
                                    }else{
                                        swal("Error!", data.message, "error");
                                    }
                                },
                                error: function () {
                                    swal("Sorry!", "something went wrong please content to admin", "error");
                                }
                            });
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
                            $("#destination_warehouse_id").html("");
                            $('#destination_warehouse_id').selectpicker("refresh");
                        }
                    },
                    error: function () {
                        alert('error occur');
                    }
                });

            }
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <div class="row">
            <div class="col-md-4">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            Add Country Bagging
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="alert" id="general-response-message" style="display: none;"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Bag Type</label><br />
                                    <input  name="bag_type" id="bag_type" type="checkbox" class="make-switch" data-on-text="Fixed" data-off-text="Random" data-on-color="primary" data-off-color="danger">
                                </div>
                            </div>
                            <div id="fixed_div" style="display: none;">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label><input type="checkbox" class="icheck" id="country_wise_bag" name="country_wise_bag" data-checkbox="icheckbox_square-blue"></label> <label>Country</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label><input type="checkbox" class="icheck" id="service_wise_bag" name="service_wise_bag" data-checkbox="icheckbox_square-blue"></label> <label>Service</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="icheck-inline">
                                                <label><input type="checkbox" class="icheck" id="warehouse_wise_bag" name="warehouse_wise_bag" data-checkbox="icheckbox_square-blue"></label> <label>Warehouse</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                   Source Country  <br><span style="font-weight: 700;font-size: 15px;"><?php $country = New Country($this->userData->getCountryId());
                                   echo $country->getName(); ?></span>
                                </div>
                                <div class="col-md-6">
                                    Source Warehouse <br><span style="font-weight: 700;font-size: 15px;"><?php $warehouse = New Warehouse($this->userData->getWarehouseId());
                                        echo $warehouse->getWarehouseName(); ?></span>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Destination Country</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"> <i class="fa fa-user"></i> </div>
                                            <?php
                                            echo Ddl::generateCountryDDL('destination_country_id', $destination_country_id, 'id', ' class="form-filter bs-select form-control" required="" data-live-search="true" data-size="8" onChange=get_destination_warehouse();');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
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
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="label-control">Tracking No.</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
                                        <input onkeypress=" return checkKeyValue(event);" type="text" name="tracking_no" id="tracking_number" class="form-control inputfield_custom_style" placeholder="Tracking number"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="show_general_msg" style="display: none;">
                                <div class="col-md-12">
                                    <div class="alert alert-danger"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            Country Bagging Info
                        </div>
                        <div class="tools">
                            <a id="reload_id" href="javascript:;" class="reload" data-original-title="" title=""> </a>
                            <a href="" class="fullscreen" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body" id="flight_info_stats"></div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div id="bag_details" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Bag Details</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="label-control">Bag Actual Weight</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
                                        <input type="text" name="bag_actual_weight" id="bag_actual_weight" class="form-control" placeholder="Bag Actual Weight"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="label-control">Bag Length</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
                                        <input type="text" name="bag_length" id="bag_length" class="form-control" placeholder="Bag Length"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="label-control">Bag Width</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
                                        <input type="text" name="bag_width" id="bag_width" class="form-control" placeholder="Bag Width"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="label-control">Bag Height</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
                                        <input type="text" name="bag_height" id="bag_height" class="form-control" placeholder="Bag Height"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="bag_detail_bag_id" value="" id="bag_detail_bag_id" />
                        <input type="hidden" name="bag_detail_mawb_id" value="" id="bag_detail_mawb_id" />
                        <button type="button" class="btn btn-primary" id="update_bag">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
        <div id="create_mawb_modal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Create Mawb</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="label-control">MAWB Number</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
                                        <input type="text" name="mawb_number" id="mawb_number" class="form-control" placeholder="mawb number"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="label-account">MAWB Type</label>
                                <div class="form-group">
                                    <select class="select2" name="mawb_class" id="mawb_class">
                                        <?php
                                        foreach ($this->mawbTypeArray as $key => $mawbItem) { ?>
                                            <option value="<?php echo $key; ?>"><?php echo $mawbItem; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="create_mawb">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="parcel_detail_model" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Parcel Detail List</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="portlet light">
                                    <div class="portlet-body">
                                        <div class="table-container">
                                            <table class="table table-striped table-bordered table-hover table-condensed" id="parcel-Detail-table">
                                                <thead>
                                                <tr role="row" class="heading">
                                                    <th>Actions</th>
                                                    <th>Tracking Number</th>
                                                    <th>Dim(L x W x H)</th>
                                                    <th>Weight(KG)</th>
                                                    <th>Status</th>
                                                </tr>
                                                <tr role="row" class="filter">
                                                    <td>
                                                        <div class="margin-bottom-5">
                                                            <button class="btn btn-xs blue filter-submit btn-outline" ><i class="fa fa-search"></i> </button>
                                                            <!--                                                                <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>-->
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="bag_filter" id="bag_filter" class="form-filter" >
                                                        <input type="text" name="tracking_number" id="tracking_number_parcel" class="form-control form-filter" >
                                                    </td>
                                                    <td>

                                                    </td>
                                                    <td>
                                                        <input type="text" name="weight" id="weight" class="form-control form-filter" >
                                                    </td>
                                                    <td>

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
}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();

function createBagWithServiceId($serviceId, $sourceCountryId, $destinationCountryId,$bagLowValue,$sourceWarehouseId,$isFixed='n',$carrierId="",$bagFilter="",$destinationWarehouseId="") {
    $returnData = [];
    $user = SessionManager::getUser();
    $userAccountName = strtoupper(substr($user->getUserName(), 0, 3));
    if(empty($bagLowValue)){
        $bagnumber = $userAccountName."-". time();
    }else{
        $bagnumber = "FSBG" . time();
    }
    $bagging = new Bagging();
    $bagging->setBagnumber($bagnumber);
    $bagging->setDateCreated(time());
    $bagging->setUserId($user->getId());
    $bagging->setBagSourceCountryId($sourceCountryId);
    $bagging->setBagSourceWarehouseId($sourceWarehouseId);
    $bagging->setBagDestinationCountryId($destinationCountryId);
    $bagging->setBagDestinationWarehouseId($destinationWarehouseId);
    $bagging->setBagType("NORMAL");
    $bagging->setService($serviceId);
    $bagging->setBagValue($bagLowValue);
    $bagging->setBagStatus(1);
    $bagging->setIsClosed(0);
    $bagging->setDateUpdated(time());
    $bagging->setIsCountryBagging("y");
    $bagging->setIsFixed($isFixed);
    $bagging->setCarrierId($carrierId);
    $bagging->setBagFilter($bagFilter);
    $bagging->save();
    if ($bagging->getId() > 0) {
        $returnData['bag_id'] = $bagging->getId();
        if($bagging->getService() > 0 && in_array($bagging->getService(), array("798", "310"))){
            $ocBagLabel = new OcBagLabel();
            $bagnumber = $ocBagLabel->createOcBagNumber($bagging->getId());
        }
        $returnData['bag_number'] = $bagnumber;
    }
    return json_encode($returnData);
    die;
}
function addDataIntoMappingTable($bagId,$parcelId,$bagNumber="",$isLowValBag="lv",$trackingNumber,$serviceId) {
    $user = SessionManager::getUser();
    $returnData['status'] = "success";
    $parcelvalue = '<span class="label label-sm  label-success">LV</span>';
    if($isLowValBag == "hv")
        $parcelvalue = '<span class="label label-sm  label-info">HV</span>';

    $returnData['message'] = '<span style="font-size: 30px;color: black;">'.$isLowValBag.'</span><br />Bag Number ['.$bagNumber.'] Parcel Number ['.$trackingNumber.']';
    $parcelBaggingMappingFilter = new ParcelBaggingMappingFilter();
    $parcelBaggingMappingFilter->addFieldFilter("    parcel_id", $parcelId);
    $parcelBaggingMappingFilter->addFieldFilter("bag_id", $bagId);
    $parcelBaggingMappingObj = $parcelBaggingMappingFilter->getColumnList("parcel_id,bag_id");
    if(count($parcelBaggingMappingObj) > 0){
        $returnData['status'] = "error";
        $returnData['message'] = "Parcel is already added into bag [".$bagNumber."]";
    }else{
        // Add data into parcel_bagging_mapping
        $parcelBaggingMapping = new ParcelBaggingMapping();
        $parcelBaggingMapping->setParcelId($parcelId);
        $parcelBaggingMapping->setBagId($bagId);
        $parcelBaggingMapping->setAddedBy($user->getId());
        $parcelBaggingMapping->setAddedDate(time());
        $parcelBaggingMapping->save();
    }
    $baggingServicesMappingFilter = New BaggingServicesMappingFilter();
    $baggingServicesMappingFilter->addFieldFilter("    bag_id",$bagId);
    $baggingServicesMappingFilter->addFieldFilter("    service_id",$serviceId);
    $baggingServicesMappingObj = $baggingServicesMappingFilter->getColumnList('id');
    if(count($baggingServicesMappingObj) > 0){
    }else{
        $baggingServicesMapping= New BaggingServicesMapping();
        $baggingServicesMapping->setBagId($bagId);
        $baggingServicesMapping->setServiceId($serviceId);
        $baggingServicesMapping->save();
    }

    $baggingFilter = new BaggingFilter();
    $baggingFilterObj = $baggingFilter->getBagParcelWeightByBagId($bagId);
    $bagTotalParcelWeight = 0;
    if (count($baggingFilterObj) > 0) {
        $bagTotalParcelWeight = $baggingFilterObj[0]->getActualWeight();
    }
    $bagParcel = BaggingFilter::getParcelTotalFromBagId($bagId);
    $bagging = new Bagging($bagId);
    $bagging->setWeight($bagTotalParcelWeight);
    $bagging->setPieces($bagParcel);
    $bagging->save();
    return $returnData;
}
function getParcelListFromBagNumber($bagId,$parcelArr=false) {
    $list = array();
    $parcelListArr = array();
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
    if($parcelArr){
        return $parcelListArr;
    }else{
        return $list;
    }
}
function generateBagLabel($bagId) {
    $baglabel = new BagLabel();
    $response = $baglabel->generateBagLabel($bagId);
    if($response["STATUS"] == "SUCCESS"){
        return SETTING_MAIN_ASSETS . $response["LABEL"];
    }
   /* $user = SessionManager::getUser();
    $dataArr = [];
    if($bagId > 0){
        $bagging = new Bagging($bagId);
        $bagDestinationWarehouseId = $bagging->getBagDestinationWarehouseId();
        if($bagDestinationWarehouseId > 0){
            $warehouse = New Warehouse($bagDestinationWarehouseId);
        }
        $dataArr['bag_number'] = $bagging->getBagnumber();
        $dataArr['bag_weight'] = $bagging->getWeight();
        $dataArr['bag_actual_weight'] = $bagging->getActualWeight();
        $dataArr['bag_destination_country_id'] = $bagging->getBagDestinationCountryId();
        $serviceObj = new Services($bagging->getService());
        if(count($serviceObj) > 0){
            $dataArr['bag_service'] = $serviceObj->getName();
        }
        $parcelbaggingCount =
        $mawbParcelMappingFilter = new MawbParcelMappingFilter();
        $mawbParcelMappingFilter->addFieldFilter("    bag_id", $bagId);
        $mawbParcelMappingObj = $mawbParcelMappingFilter->getColumnList("mawb_id");
        if(count($mawbParcelMappingObj) > 0){
            $mawbId = $mawbParcelMappingObj[0]->getMawbId();
            $mawbObj = new Mawb($mawbId);
            $dataArr['bag_mawb'] = $mawbObj->getMawbNumber();
            $flighMappingFilter = new FlightMappingFilter();
            $flighMappingFilter->addFieldFilter("    mawb_id", $mawbId);
            $flighMappingFilterObj = $flighMappingFilter->getColumnList("flight_info_id");
            if(count($flighMappingFilterObj) > 0){
                $flightInfoId = $flighMappingFilterObj[0]->getFlightInfoId();
                $fligthInfo = new FlightInfo($flightInfoId);
                $countryObj = new Country($fligthInfo->getDestinationCountryId());
                if(count($countryObj) > 0){
                    $dataArr['bag_country'] = $countryObj->getName();
                }
                $dataArr['bag_address_line_1'] = $fligthInfo->getAddressLine1();
                $dataArr['bag_address_line_2'] = $fligthInfo->getAddressLine2();
                $dataArr['bag_city'] = $fligthInfo->getCity();
                $dataArr['bag_postcode'] = $fligthInfo->getPostcode();
                $dataArr['bag_company'] = $fligthInfo->getCompany();
            }
        }
    }
    if (count($dataArr) > 0) {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetPrintFooter(false);
        $pdf->SetFooterMargin(0);
        $pdf->SetPrintHeader(false);
        $pdf->SetAutoPageBreak(false, 0);
        $page_size = array(150, 100);
        $pdf->AddPage("P", $page_size);
        $pdf->setFont("helvetica", "L", 7);
        $userImage = User::getUserCompanyImages(true,$user->getId());
        $userImage = explode('images/', $userImage);
        $userImage = "../images/".$userImage[1];
        $pdf->Image($userImage,5,2,40,20);
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => true,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => '1',
            'vpadding' => '1',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => false,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 1
        );
        $pdf->SetFont('Times', 'B', 10);
        $addressline1 = $dataArr['bag_address_line_1'];
        $addressline2 = $dataArr['bag_address_line_2'];
//        $addressline3 = $dataArr['bag_address_line_3'];
        $addressline4 = "";
        $city = $dataArr['bag_city'];
        $postcode = $dataArr['bag_postcode'];
        $countryname = $dataArr['bag_country'];
        $company = $dataArr['bag_company'];
        $y = 25;
        $pdf->Text(12, $y, strtoupper("TO:"));
        if ($company != '') {
            $y += 5;
            $pdf->Text(12, $y, strtoupper($company));
        }else if(!empty($warehouse)){
            $y += 5;
            $pdf->Text(12, $y, strtoupper($warehouse->getWarehouseName()));
        }
        if ($addressline1 != '') {
            $y += 5;
            $pdf->Text(12, $y, strtoupper($addressline1));
        }else if(!empty($warehouse)){
            $y += 5;
            $pdf->Text(12, $y, strtoupper($warehouse->getAddressline1()));
        }
        if ($addressline2 != '') {
            $y += 5;
            $pdf->Text(12, $y, strtoupper($addressline2));
        }else if(!empty($warehouse)){
            $y += 5;
            $pdf->Text(12, $y, strtoupper($warehouse->getAddressline2()));
        }
//        if ($addressline3 != '') {
//            $y += 5;
//            $pdf->Text(12, $y, strtoupper($addressline3));
//        }
//        if ($addressline4 != '') {
//            $y += 5;
//            $pdf->Text(12, $y, strtoupper($addressline4));
//        }
        if ($city != '') {
            $y += 5;
            $pdf->Text(12, $y, strtoupper($city));
        }else if(!empty($warehouse)){
            $y += 5;
            $pdf->Text(12, $y, strtoupper($warehouse->getCitytown()));
        }
        if ($postcode != '') {
            $y += 5;
            $pdf->Text(12, $y, strtoupper($postcode));
        }else if(!empty($warehouse)){
            $y += 5;
            $pdf->Text(12, $y, strtoupper($warehouse->getPostzipcode()));
        }
        if ($countryname != '') {
            $y += 5;
            $pdf->Text(12, $y, strtoupper($countryname));
        }
        else if($dataArr['bag_destination_country_id'] != ''){
            $destinationCountry = new Country($dataArr['bag_destination_country_id']);
            $pdf->SetFont('Times', 'B', 15);
            $pdf->Text(25, 55, strtoupper($destinationCountry->getName()));
        }
        $pdf->SetFont('Times', 'B', 12);
        $pdf->Text(20, 68, "MAWB");
        $pdf->Text(20, 78, "Pieces");
        $pdf->Text(20, 88, "Tag No.");
        $pdf->Text(20, 98, "Weight");
        $pdf->Text(20, 108, "Actual Weight");
//        $pdf->Text(20, 108, "Service");
        $pdf->line(50, 65, 50, 115);   //centerline
        $pdf->line(10, 65, 90, 65); // line 2
        $pdf->line(10, 75, 90, 75); // line 3
        $pdf->line(10, 85, 90, 85); // line 4
        $pdf->line(10, 95, 90, 95); // line 5
        $pdf->line(10, 105, 90, 105);
        $pdf->line(10, 115, 90, 115);
        $pdf->line(10, 25, 90, 25);
        $pdf->line(10, 25, 10, 115);
        $pdf->line(90, 25, 90, 115);
        $pdf->Text(55, 68, $dataArr['bag_mawb']);
        $parcelCount = getParcelListFromBagNumber($bagId,true);
        $pdf->Text(55, 78, count($parcelCount));
        $pdf->Text(55, 88, $dataArr['bag_number']);
        $pdf->Text(55, 98, $dataArr['bag_weight'] . " Kg");
        $pdf->Text(55, 108, $dataArr['bag_actual_weight'] . " Kg");
        $pdf->SetFont('Times', 'B', 10);
       // $pdf->Text(20, 108, $dataArr['bag_service']);
        $pdf->SetFont('Times', 'B', 12);
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->write1DBarcode($dataArr['bag_number'], 'C128', 10, 120, 80, 18, 0.4, '', 'C');
        $folderPath = '../_assets/export_manifest/';
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
        $fileName = time()."_".$dataArr['bag_number'].'.pdf';
        $outFile = $folderPath.$fileName;
        $filePathDb = SETTING_MAIN_ASSETS."export_manifest/".$fileName;
        $pdf->Output($outFile, 'F');
        $bagging->setBagLabel($filePathDb);
        $bagging->save();
        return $filePathDb;
    }*/
}
function generateInvoice($conArr) {
    $pdf2 = new PDFMerger();
    $mergeFileName = "manifest-invoice-".time(). ".pdf";
    $folderPath = '../_assets/export_manifest/';
    if (!file_exists($folderPath)) {
        mkdir($folderPath, 0777, true);
    }
    foreach($conArr as $consignemtId )
    {
        $consignment = new Consignment($consignemtId); //	$consignmentData[0];
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetX(1.0);
        $glOrderPdf = new ProformaInvoice($pdf);
        $glOrderPdf->setInvoiceFolder("export_manifest");
        $glOrderPdf->setIsReturnUrl(false);
        $invoiceLink = $glOrderPdf->AddHTML($consignment,'', false, true);
        $pdf2->addPDF($invoiceLink, 'all');
        $conLabel = explode(".",$consignment->getLabelFile());
        $labelLinl  = "../_assets/pdf/".$conLabel[0].".pdf";
        if(file_exists($labelLinl)){
            try{
                $pdf2->addPDF($labelLinl, 'all');
            }catch (Exception $e){

            }
        }
    }
    $filePatch      =       $mergeFileName;
//        $newFileVarUrls	=	SETTING_MAIN_ASSETS."export_manifest/".$filePatch;
    $newFileVar	=	$folderPath.$filePatch;
    try {
        $pdf2->merge('file',$newFileVar);
    }catch (Exception $e){

    }
    return "export_manifest/".$filePatch;
}
function getExcelFile($flightList,$HLValue="lv") {
    //date_default_timezone_set('Europe/London');
    /** PHPExcel */
    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
    $excel_column = 1;

    $FontBoldArray = array(
        'font' => array(
            'bold' => true,
            'size' => 10,
            'name' => 'Calibri',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
    );

    $border = array(
        'borders' => array(
            'top' => array(
                'style' => 'thick'
            ),
            'bottom' => array(
                'style' => 'thick'
            )
        )
    );
    $borderBottom = array(
        'borders' => array(
            'bottom' => array(
                'style' => 'thick'
            )
        )
    );
    $borderThinBottom = array(
        'borders' => array(
            'bottom' => array(
                'style' => 'thin'
            )
        )
    );
    $borderThinRight = array(
        'borders' => array(
            'right' => array(
                'style' => 'thin'
            )
        )
    );
    $borderThinRightBottom = array(
        'borders' => array(
            'right' => array(
                'style' => 'thin'
            ),
            'bottom' => array(
                'style' => 'thin'
            )
        )
    );
    $borderTop = array(
        'borders' => array(
            'top' => array(
                'style' => 'thick'
            )
        )
    );
    $topFiveBox =  array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $topText = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 22,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $columnNine = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
    );
    $bold = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
    );
    $columnTen = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 12,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
    );
    $h12Mawb = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 12,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        ),
    );
    $alignCenterTopBottom = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $alignCenterTopBottomRight = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $alignCenterTopBottomRight2 = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => 'F2F2F2'),
            'size' => 12,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $alignCenterTopBottomLeft = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $alignCenterTopBottomLeftBold = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 10,
            'name' => 'Times New Roman',
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'F2F2F2')
        ),
    );
    $lastTable = array(
        'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        ),
        'font' => array(
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Calibri',
        ),
    );
    $lastHeader = array(
        'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 10,
            'name' => 'Calibri',
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'F2F2F2')
        ),
    );
    $lastHeaderYellow = array(
        'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 10,
            'name' => 'Calibri',
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'FFF71C')
        ),
    );
    $alignCenterTopBottomRightBold = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $alignCenterTopBottomRightBoldExtraa = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 12,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $columnTenPlus = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $columnNineH = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        ),
    );
    // Set properties
    $objPHPExcel->getProperties()->setCreator("One World Express ")
        ->setLastModifiedBy("Operation Syste,")
        ->setTitle("Office 2007 XLSX Sales Invoice")
        ->setSubject("Office 2007 XLSX Sales Invoice")
        ->setDescription("This document is generated from the system, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Sales Invoice");
    // Add some data
    $objPHPExcel->getActiveSheet()->getColumnDimension("A1")->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension("B1")->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension("C1")->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension("D1")->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension("E1")->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension("F1")->setWidth(8);
    $objPHPExcel->getActiveSheet()->getColumnDimension("G1")->setWidth(18);
    $objPHPExcel->getActiveSheet()->getColumnDimension("H1")->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension("I1")->setWidth(18);
    $objPHPExcel->getActiveSheet()->getColumnDimension("J1")->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension("K1")->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension("L1")->setWidth(16);
    $objPHPExcel->getActiveSheet()->getColumnDimension("M1")->setWidth(16);
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', 'APPLICATION FOR RELEASE OF GOODS IN TERMS OF SECTION 38 (1) (a) OF THE')->mergeCells('A1:J1')
        ->setCellValue('A2', 'CUTOMS AND EXCISE ACT , ACT NUMBER 91 OF 1964')->mergeCells('A2:J2')
        ->setCellValue('A3', '')->mergeCells('A3:J3')
        ->setCellValue('A4', 'AANSOEK OM LOSSING VAN GOEDERE INGEVLOGE ARTIKEL 38 (1) (a) VAN DIE')->mergeCells('A4:J4')
        ->setCellValue('A5', 'DOEANE  EN AKSYNSWET, WET NOMMER 91 VAN 1964')->mergeCells('A5:J5')
        ->setCellValue('A6', '')->mergeCells('A6:M6')
        ->setCellValue('A7', 'Name and Address of Importer:')->mergeCells('A7:M7')
        ->setCellValue('A8', 'RT CLEARING AND FORWARDING')->mergeCells('A8:M8')
        ->setCellValue('A9', 'TRANSPORT DOCUMENT NUMBER AND DATE')->mergeCells('A9:G9')
        ->setCellValue('A10', 'VERVOERDOKUMENT NOMMER EN DATUM')->mergeCells('A10:G10')
        ->setCellValue('H12', $flightList['flight_number']."/ ".date("d/m/Y"))->mergeCells('H12:J12')
        ->setCellValue('H9', 'SHIP AND VOYAGE NO / FLIGHT NO AND DATE')->mergeCells('H9:M9')
        ->setCellValue('H10', 'SKIP EN VAARTNR /  VLUGNR EN DATUM')->mergeCells('H10:M10');
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('K1', 'DA 306')->mergeCells('K1:M4')
        ->setCellValue('K5', '')->mergeCells('K5:M5');
    $objPHPExcel->getActiveSheet()->getStyle('A1:J5')->applyFromArray($topFiveBox);
    $objPHPExcel->getActiveSheet()->getStyle('K1:M5')->applyFromArray($topText);
    $objPHPExcel->getActiveSheet()->getStyle('A7:M7')->applyFromArray($columnNine);
    $objPHPExcel->getActiveSheet()->getStyle('A8:M8')->applyFromArray($columnNine);
    $objPHPExcel->getActiveSheet()->getStyle('A9:G10')->applyFromArray($columnTenPlus);
    $objPHPExcel->getActiveSheet()->getStyle('H9:M10')->applyFromArray($columnNineH);
    // Miscellaneous glyphs, UTF-8
    $mawblistHLV = 'mawb_list_'.$HLValue;
    if (count($flightList[$mawblistHLV]) > 0) {
        $countMawbDataLine = 12;
        foreach ($flightList[$mawblistHLV] as $flightData) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' .$countMawbDataLine, $flightList['flight_number']." - ".$flightData)->mergeCells('A'. $countMawbDataLine.':G'.$countMawbDataLine);
            $countMawbDataLine++;
        }
    }
    $totalForeignValueIndex = 'flight_total_parcel_value_'.$HLValue;
    $totalParcelForeignValue = $flightList[$totalForeignValueIndex];
    $cssColumnCount = $countMawbDataLine-1;
    $objPHPExcel->getActiveSheet()->getStyle('A12:G'.$cssColumnCount)->applyFromArray($columnTen);
    $objPHPExcel->getActiveSheet()->getStyle('H11:J'.$cssColumnCount)->applyFromArray($h12Mawb);
    $cssColumnCount = $countMawbDataLine+1;
    $objPHPExcel->getActiveSheet()->getStyle('A'.$countMawbDataLine.':G'.$cssColumnCount)->applyFromArray($alignCenterTopBottomRight);
    $objPHPExcel->getActiveSheet()->getStyle('H'.$countMawbDataLine.':I'.$cssColumnCount)->applyFromArray($alignCenterTopBottomRight);
    $objPHPExcel->getActiveSheet()->getStyle('J'.$countMawbDataLine.':M'.$countMawbDataLine)->applyFromArray($alignCenterTopBottomRight);
    $objPHPExcel->getActiveSheet()->getStyle('J'.$cssColumnCount.':M'.$cssColumnCount)->applyFromArray($alignCenterTopBottomRight);
    $cssColumnCount = $cssColumnCount+1;
//        $cssColumnCountNext = $cssColumnCount+1;
    $objPHPExcel->getActiveSheet()->getStyle('A'.$cssColumnCount.':G'.$cssColumnCount)->applyFromArray($alignCenterTopBottomRight2);
    $objPHPExcel->getActiveSheet()->getStyle('H'.$cssColumnCount.':I'.$cssColumnCount)->applyFromArray($alignCenterTopBottomRightBold);
    $objPHPExcel->getActiveSheet()->getStyle('J'.$cssColumnCount.':M'.$cssColumnCount)->applyFromArray($alignCenterTopBottom);
    $cssColumnCount = $cssColumnCount+1;
    $cssColumnCountNext = $cssColumnCount+3;
    $objPHPExcel->getActiveSheet()->getStyle('A'.$cssColumnCount.':G'.$cssColumnCountNext)->applyFromArray($alignCenterTopBottomRight);
    $objPHPExcel->getActiveSheet()->getStyle('H'.$cssColumnCount.':M'.$cssColumnCountNext)->applyFromArray($alignCenterTopBottom);
    $cssColumnCount = $cssColumnCountNext+1;
    $cssColumnCountNext = $cssColumnCount+2;
    $objPHPExcel->getActiveSheet()->getStyle('A'.$cssColumnCount.':G'.$cssColumnCountNext)->applyFromArray($alignCenterTopBottomRightBoldExtraa);
    $objPHPExcel->getActiveSheet()->getStyle('H'.$cssColumnCount.':M'.$cssColumnCountNext)->applyFromArray($alignCenterTopBottom);
    $cssColumnCount = $cssColumnCountNext+2;
    $objPHPExcel->getActiveSheet()->getStyle('N'.$cssColumnCount.':O'.$cssColumnCount)->applyFromArray($alignCenterTopBottomLeftBold);
    $cssColumnCount = $cssColumnCount+1;
    $objPHPExcel->getActiveSheet()->getStyle('N'.$cssColumnCount.':O'.$cssColumnCount)->applyFromArray($alignCenterTopBottomLeftBold);
    $cssColumnCount = $cssColumnCount+1;
    $objPHPExcel->getActiveSheet()->getStyle('A'.$cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('C'.$cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('D'.$cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('E'.$cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('F'.$cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('G'.$cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('H'.$cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('I'.$cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('J'.$cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('K'.$cssColumnCount)->applyFromArray($lastHeaderYellow);
    $objPHPExcel->getActiveSheet()->getStyle('L'.$cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('M'.$cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('N'.$cssColumnCount)->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('O'.$cssColumnCount)->applyFromArray($lastHeader);

    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, 'TOTAL NUMBER OF PACKAGES')->mergeCells('A'.$countMawbDataLine.':G'.$countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$countMawbDataLine, 'CUSTOMS VALUE')->mergeCells('H'.$countMawbDataLine.':I'.$countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$countMawbDataLine, 'IMPORT PERMIT NO AND AMOUNT')->mergeCells('J'.$countMawbDataLine.':M'.$countMawbDataLine);

    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, 'TOTALE GETAL PAKKE')->mergeCells('A'.$countMawbDataLine.':G'.$countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$countMawbDataLine, 'DOEANEWAARDE')->mergeCells('H'.$countMawbDataLine.':I'.$countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$countMawbDataLine, 'INVOERPERMITNR EN BEDRAG')->mergeCells('J'.$countMawbDataLine.':M'.$countMawbDataLine);

    $countMawbDataLine++;
    $flightTotalParcelIndex = "flight_total_parcel_".$HLValue;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, $flightList[$flightTotalParcelIndex])->mergeCells('A'.$countMawbDataLine.':G'.$countMawbDataLine);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$countMawbDataLine)->applyFromArray($h12Mawb);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$countMawbDataLine, 'N.C.V')->mergeCells('H'.$countMawbDataLine.':I'.$countMawbDataLine);

    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, 'MARKS, NUMBERS AND DESCRIPTION OF PACKAGES AND / OR')->mergeCells('A'.$countMawbDataLine.':G'.$countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$countMawbDataLine, '')->mergeCells('H'.$countMawbDataLine.':M'.$countMawbDataLine);

    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, 'CONTAINER NUMBER(S)')->mergeCells('A'.$countMawbDataLine.':G'.$countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$countMawbDataLine, 'DESCRIPTION OF GOODS')->mergeCells('H'.$countMawbDataLine.':M'.$countMawbDataLine);

    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, 'MERKE, NOMMERS EN BESKRYWING VAN PAKKE EN / OF')->mergeCells('A'.$countMawbDataLine.':G'.$countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$countMawbDataLine, 'BESKRYWING VAN GOEDERE')->mergeCells('H'.$countMawbDataLine.':M'.$countMawbDataLine);

    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, 'HOUERNOMMER(S)')->mergeCells('A'.$countMawbDataLine.':G'.$countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$countMawbDataLine, '')->mergeCells('H'.$countMawbDataLine.':M'.$countMawbDataLine);

    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine,'')->mergeCells('A'.$countMawbDataLine.':G'.$countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$countMawbDataLine, '')->mergeCells('H'.$countMawbDataLine.':M'.$countMawbDataLine);

    $countMawbDataLine++;
    $flightTotalParcelWeightIndex = "flight_total_parcel_weight_".$HLValue;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, $flightList[$flightTotalParcelWeightIndex]." KG")->mergeCells('A'.$countMawbDataLine.':G'.$countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$countMawbDataLine, 'DOCUMENTS / ASSORTED / DUITABLES')->mergeCells('H'.$countMawbDataLine.':M'.$countMawbDataLine);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$countMawbDataLine, 'TOTAL DUITABLE GBP '.$totalParcelForeignValue)->mergeCells('H'.$countMawbDataLine.':M'.$countMawbDataLine);

    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, '')->mergeCells('A'.$countMawbDataLine.':G'.$countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$countMawbDataLine, '')->mergeCells('H'.$countMawbDataLine.':M'.$countMawbDataLine);

    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, '')->mergeCells('A'.$countMawbDataLine.':G'.$countMawbDataLine);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$countMawbDataLine, '')->mergeCells('H'.$countMawbDataLine.':M'.$countMawbDataLine);

    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N'.$countMawbDataLine, 'SAR USE ONLY')->mergeCells('N'.$countMawbDataLine.':O'.$countMawbDataLine);

    $countMawbDataLine++;
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N'.$countMawbDataLine, '')->mergeCells('N'.$countMawbDataLine.':O'.$countMawbDataLine);
//        $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, 'FLIGHT NUMBER')
        ->setCellValue('B'.$countMawbDataLine, 'MAWB')
        ->setCellValue('C'.$countMawbDataLine, 'HAWB')
        ->setCellValue('D'.$countMawbDataLine, 'Origin')
        ->setCellValue('E'.$countMawbDataLine, 'Shipper')
        ->setCellValue('F'.$countMawbDataLine, 'Dest')
        ->setCellValue('G'.$countMawbDataLine, 'Consignee')
        ->setCellValue('H'.$countMawbDataLine, 'Weight')
        ->setCellValue('I'.$countMawbDataLine, 'Description')
        ->setCellValue('J'.$countMawbDataLine, 'Pieces')
        ->setCellValue('K'.$countMawbDataLine, 'Foreign Value (£)')
        ->setCellValue('L'.$countMawbDataLine, ' Customs Value (R)')
        ->setCellValue('M'.$countMawbDataLine, '20% Duty')
        ->setCellValue('N'.$countMawbDataLine, 'OGA')
        ->setCellValue('O'.$countMawbDataLine, 'Detain');
    //Data from flight table
    $arrayIndex = "flight_".$HLValue."_data";
    foreach ($flightList[$arrayIndex] as $mawbData) {
        $countMawbDataLine++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, $flightList['flight_number'])
            ->setCellValue('B'.$countMawbDataLine, $mawbData['mawb'])
            ->setCellValue('C'.$countMawbDataLine, $mawbData['hawb'])
            ->setCellValue('D'.$countMawbDataLine, $mawbData['from_country'])
            ->setCellValue('E'.$countMawbDataLine, $mawbData['sender_name'])
            ->setCellValue('F'.$countMawbDataLine, $mawbData['to_country'])
            ->setCellValue('G'.$countMawbDataLine, $mawbData['receiver_name'])
            ->setCellValue('H'.$countMawbDataLine, $mawbData['weight'])
            ->setCellValue('I'.$countMawbDataLine, $mawbData['description'])
            ->setCellValue('J'.$countMawbDataLine, $mawbData['no_of_pieces'])
            ->setCellValue('K'.$countMawbDataLine, $mawbData['foreign_value'])
            ->setCellValue('L'.$countMawbDataLine, $mawbData['custom_value'])
            ->setCellValue('M'.$countMawbDataLine, $mawbData['duty'])
            ->setCellValue('N'.$countMawbDataLine, '')
            ->setCellValue('O'.$countMawbDataLine, '');
        $objPHPExcel->getActiveSheet()->getStyle('A'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('E'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('F'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('G'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('H'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('I'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('J'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('K'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('L'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('M'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('N'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('O'.$countMawbDataLine)->applyFromArray($lastTable);
    }
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(17);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(17);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(17);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(14);
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);

    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, 'Name and Surname')->mergeCells('A'.$countMawbDataLine.':O'.$countMawbDataLine);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, 'FOR THE IMPORTER HEAR BY APPLY FOR THE RELEASE OF THE ABOVE MENTIONED GOODS IN TERMS OF')->mergeCells('A'.$countMawbDataLine.':O'.$countMawbDataLine);$countMawbDataLine++;
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, 'SECTION 38 (1)(A) AND DECLARE THAT THE PARTICULARS HEREIN ARE TRUE AND CORRECT  AND COMPLY')->mergeCells('A'.$countMawbDataLine.':O'.$countMawbDataLine);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, 'WITH THE  PROVISIONS OF THE CUSTOMS AND EXCISE ACT.')->mergeCells('A'.$countMawbDataLine.':O'.$countMawbDataLine);
    $countMawbDataLine += 3;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, 'NAMENS INVOERDER DOEN HIERMEE AANSOEK OM LOSSING VAN DIE BOGENOEMDE GOEDERE INGEVOLGE')->mergeCells('A'.$countMawbDataLine.':O'.$countMawbDataLine)->getStyle()->getFont()->setBold(true);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, 'ARTIKEL 38 (1) (a) EN VERKLAAR DAT DIE BESONDERHEDE HIERIN WAAR EN KORREK IS EN AAN DIE')->mergeCells('A'.$countMawbDataLine.':O'.$countMawbDataLine)->getStyle()->getFont()->setBold(true);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, 'PEPALINGS VAN DIE DOEANE EN  AKSYNSWET VOLDOEN')->mergeCells('A'.$countMawbDataLine.':O'.$countMawbDataLine)->getStyle()->getFont()->setBold(true);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, 'DATE');
    $objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$countMawbDataLine)->getFont()->setBold(true);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.$countMawbDataLine, 'Signature / Handtekening')->mergeCells('M'.$countMawbDataLine.':O'.$countMawbDataLine)->getStyle()->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$countMawbDataLine.':O'.$countMawbDataLine)->applyFromArray($borderThinBottom);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, 'INSTRUCTIONS BY THE CONTROLLER')->mergeCells('A'.$countMawbDataLine.':F'.$countMawbDataLine)->getStyle()->applyFromArray($borderThinRight);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$countMawbDataLine, '')->mergeCells('G'.$countMawbDataLine.':J'.$countMawbDataLine)->getStyle()->applyFromArray($borderThinRight);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$countMawbDataLine, '')->mergeCells('K'.$countMawbDataLine.':O'.$countMawbDataLine)->getStyle()->applyFromArray($borderThinRight);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, 'OF CUSTOMS AND EXCISE')->mergeCells('A'.$countMawbDataLine.':F'.$countMawbDataLine)->getStyle()->applyFromArray($borderThinRight);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$countMawbDataLine, 'ENDORSEMENTS')->mergeCells('G'.$countMawbDataLine.':J'.$countMawbDataLine)->getStyle()->applyFromArray($borderThinRight)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$countMawbDataLine, 'PLACE OF ENTRY')->mergeCells('K'.$countMawbDataLine.':O'.$countMawbDataLine)->getStyle()->applyFromArray($borderThinRight)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, 'OPDRAG DEUR DIE KONTROLEUR VAN')->mergeCells('A'.$countMawbDataLine.':F'.$countMawbDataLine)->getStyle()->applyFromArray($borderThinRight);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$countMawbDataLine, 'ENDOSSEMENTE')->mergeCells('G'.$countMawbDataLine.':J'.$countMawbDataLine)->getStyle()->applyFromArray($borderThinRight);
    $objPHPExcel->setActiveSheetIndex(0)->getStyle('G'.$countMawbDataLine)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$countMawbDataLine, 'KLARINGSPLEK')->mergeCells('K'.$countMawbDataLine.':O'.$countMawbDataLine)->getStyle()->applyFromArray($borderThinRight);
    $objPHPExcel->setActiveSheetIndex(0)->getStyle('K'.$countMawbDataLine)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, 'DOEANE EN AKSYNS')->mergeCells('A'.$countMawbDataLine.':F'.$countMawbDataLine)->getStyle()->applyFromArray($borderThinRightBottom);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$countMawbDataLine, '')->mergeCells('G'.$countMawbDataLine.':J'.$countMawbDataLine)->getStyle()->applyFromArray($borderThinRightBottom);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$countMawbDataLine, '')->mergeCells('K'.$countMawbDataLine.':O'.$countMawbDataLine)->getStyle()->applyFromArray($borderThinRightBottom);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, '')->mergeCells('A'.$countMawbDataLine.':J'.$countMawbDataLine)->getStyle()->applyFromArray($borderThinRight);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$countMawbDataLine, 'J.S.A.')->mergeCells('K'.$countMawbDataLine.':O'.$countMawbDataLine)->getStyle()->applyFromArray($borderThinRight)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->setActiveSheetIndex(0)->getStyle('K'.$countMawbDataLine)->getFont()->setBold(true);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, '')->mergeCells('A'.$countMawbDataLine.':J'.$countMawbDataLine)->getStyle()->applyFromArray($borderThinRight);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$countMawbDataLine, '')->mergeCells('K'.$countMawbDataLine.':O'.$countMawbDataLine)->getStyle()->applyFromArray($borderThinRightBottom);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, '')->mergeCells('A'.$countMawbDataLine.':J'.$countMawbDataLine)->getStyle()->applyFromArray($borderThinRight);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$countMawbDataLine, 'NUMBER AND DATE')->mergeCells('K'.$countMawbDataLine.':O'.$countMawbDataLine)->getStyle()->applyFromArray($borderThinRight)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $countMawbDataLine++;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, '')->mergeCells('A'.$countMawbDataLine.':J'.$countMawbDataLine)->getStyle()->applyFromArray($borderThinRight);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$countMawbDataLine, 'NOMMER EN DATEM ')->mergeCells('K'.$countMawbDataLine.':O'.$countMawbDataLine)->getStyle()->applyFromArray($borderThinRightBottom)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $folder_path = "../_assets/";
    if (!file_exists($folder_path."export_manifest")) {
        mkdir($folder_path, 0777, true);
    }
    // Rename sheet
    $objPHPExcel->getActiveSheet()->setTitle('Export SA Manifest');
    $fileName = "export_manifest/export-SA-".strtoupper($HLValue)."-manifest-" . time(). rand('1', '10000') . ".xlsx";
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    //$objPHPExcel->setActiveSheetIndex(0);
    // Redirect output to a clientï¿½s web browser (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename='.$fileName.'');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    ob_clean();
    $objWriter->save($folder_path.$fileName);
    return $fileName;
}
function getBagManifestExcel($bagId) {
    $bagging = new Bagging($bagId);
    $parcelBaggingMappingFilter = new ParcelBaggingMappingFilter();
    $parcelBaggingMappingFilter->addFieldFilter("    bag_id", $bagId);
    $parcelBaggingMappingObj = $parcelBaggingMappingFilter->getList();
    if(count($parcelBaggingMappingObj) > 0){
        $baggingData = [];
        $shipper = "";
        $consignee = "";
        foreach ($parcelBaggingMappingObj as $parcelBaggingMappingArr) {
            $parcelObj = new Parcel($parcelBaggingMappingArr->getParcelId());
            $consignmentObj = new Consignment($parcelObj->getConsignmentId());
            $senderCountry = new Country($consignmentObj->getSenderCountryId());
            $consigneeCountry = new Country($consignmentObj->getCountryId());
            $senderCountryName = $senderCountry->getName();
            $consigneeCountryName = $consigneeCountry->getName();

            $shipper = $consignmentObj->getSenderCompany()." ".$consignmentObj->getSenderAddressLine1()." ".$consignmentObj->getSenderAddressLine2()." ".$consignmentObj->getSenderAddressLine3()." ".$consignmentObj->getSenderCity()." ".$consignmentObj->getSenderPostcode()." ".$senderCountryName;

            $consignee = $consignmentObj->getCompany()." ".$consignmentObj->getAddressLine1()." ".$consignmentObj->getAddressLine2()." ".$consignmentObj->getAddressLine3()." ".$consignmentObj->getCity()." ".$consignmentObj->getPostcode()." ".$consigneeCountryName;
            $baggingData[$bagging->getBagnumber()][$parcelObj->getTrackingNumber()] = [$shipper,$consignee,$parcelObj->getWeight(),$parcelObj->getItemvalue()." ".$consignmentObj->getCurrency(),$consignmentObj->getDescription()];
        }
    }

    //date_default_timezone_set('Europe/London');
    /** PHPExcel */
    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
    $excel_column = 1;

    $FontBoldArray = array(
        'font' => array(
            'bold' => true,
            'size' => 10,
            'name' => 'Calibri',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
    );

    $border = array(
        'borders' => array(
            'top' => array(
                'style' => 'thick'
            ),
            'bottom' => array(
                'style' => 'thick'
            )
        )
    );
    $borderBottom = array(
        'borders' => array(
            'bottom' => array(
                'style' => 'thick'
            )
        )
    );
    $borderThinBottom = array(
        'borders' => array(
            'bottom' => array(
                'style' => 'thin'
            )
        )
    );
    $borderTop = array(
        'borders' => array(
            'top' => array(
                'style' => 'thick'
            )
        )
    );
    $topFiveBox =  array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $topText = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 22,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $columnNine = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
    );
    $bold = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
    );
    $columnTen = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
    );
    $alignCenterTopBottom = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $alignCenterTopBottomRight = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $alignCenterTopBottomLeft = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $alignCenterTopBottomLeftBold = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 10,
            'name' => 'Times New Roman',
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'F2F2F2')
        ),
    );
    $lastTable = array(
        'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        ),
        'font' => array(
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Calibri',
        ),
    );
    $lastHeader = array(
        'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 10,
            'name' => 'Calibri',
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'F2F2F2')
        ),
    );
    $lastHeaderYellow = array(
        'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 10,
            'name' => 'Calibri',
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'FFF71C')
        ),
    );
    $alignCenterTopBottomRightBold = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $alignCenterTopBottomRightLeftBold = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $alignCenterTopBottomRightLeftThin = array(
        'font' => array(
//                'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
//            'alignment' => array(
//                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
//                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
//            ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        ),
    );
    $alignCenterTopBottomRightLeftThinNew = array(
        'font' => array(
//                'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
        'alignment' => array(
//                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        ),
    );
    $alignCenterTopBottomRightBoldExtra = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 12,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $columnTenPlus = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
    );
    $columnNineH = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Arial',
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        ),
    );
    // Set properties
    $objPHPExcel->getProperties()->setCreator("One World Express ")
        ->setLastModifiedBy("Operation Syste,")
        ->setTitle("Office 2007 XLSX Sales Invoice")
        ->setSubject("Office 2007 XLSX Sales Invoice")
        ->setDescription("This document is generated from the system, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Sales Invoice");
    $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(45);
    $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(45);
    $objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(25);
    // Add some data
    $objPHPExcel->getActiveSheet()->getStyle('A1:B1')->applyFromArray($alignCenterTopBottomRightBold);
    $objPHPExcel->getActiveSheet()->getStyle('G1:H1')->applyFromArray($alignCenterTopBottomRightLeftBold);
    $objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray($alignCenterTopBottomRightLeftBold);
    $objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($alignCenterTopBottomRightLeftBold);
    $objPHPExcel->getActiveSheet()->getStyle('D3')->applyFromArray($alignCenterTopBottomRightLeftBold);
    $objPHPExcel->getActiveSheet()->getStyle('E3')->applyFromArray($alignCenterTopBottomRightLeftBold);
    $objPHPExcel->getActiveSheet()->getStyle('F3')->applyFromArray($alignCenterTopBottomRightLeftBold);
    $objPHPExcel->getActiveSheet()->getStyle('G3')->applyFromArray($alignCenterTopBottomRightLeftBold);
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Tag: '.$bagging->getBagnumber())->mergeCells('A1:B1')
        ->setCellValue('G1', 'Date: '.DATE("Y-m-d"))->mergeCells('G1:H1')
        ->setCellValue('B3', 'Shipment Details')
        ->setCellValue('C3', 'Shipper')
        ->setCellValue('D3', 'Consignee')
        ->setCellValue('E3', 'Weight')
        ->setCellValue('F3', 'Value')
        ->setCellValue('G3', 'Description')
    ;
    // Miscellaneous glyphs, UTF-8
    if (count($baggingData) > 0) {
        $countMawbDataLine = 4;
        $totalParcel = 0;
        $totalWeight = 0;
        $totalValue = 0;
        foreach ($baggingData as $bagNumber => $conDataArr) {
            $totalParcel = count($conDataArr);
            foreach ($conDataArr as $parcelTracking => $parcelData) {
                $totalWeight += $parcelData['2'];
                $totalValue += $parcelData['3'];
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('B' . $countMawbDataLine, 'Tracking : '.$parcelTracking)
                    ->setCellValue('C' . $countMawbDataLine, $parcelData['0'])
                    ->setCellValue('D' . $countMawbDataLine, 'c/o '.$parcelData['1'])
                    ->setCellValue('E' . $countMawbDataLine, $parcelData['2'])
                    ->setCellValue('F' . $countMawbDataLine, $parcelData['3'])
                    ->setCellValue('G' . $countMawbDataLine, $parcelData['4']);
                $objPHPExcel->getActiveSheet()->getStyle('B'.$countMawbDataLine)->applyFromArray($alignCenterTopBottomRightLeftThinNew);
                $objPHPExcel->getActiveSheet()->getStyle('C'.$countMawbDataLine)->applyFromArray($alignCenterTopBottomRightLeftThin);
                $objPHPExcel->getActiveSheet()->getStyle("C".$countMawbDataLine)->getAlignment()->setWrapText(true);

                $objPHPExcel->getActiveSheet()->getStyle('D'.$countMawbDataLine)->applyFromArray($alignCenterTopBottomRightLeftThin);
                $objPHPExcel->getActiveSheet()->getStyle("D".$countMawbDataLine)->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->getStyle('E'.$countMawbDataLine)->applyFromArray($alignCenterTopBottomRightLeftThin);
                $objPHPExcel->getActiveSheet()->getStyle('F'.$countMawbDataLine)->applyFromArray($alignCenterTopBottomRightLeftThin);
                $objPHPExcel->getActiveSheet()->getStyle('G'.$countMawbDataLine)->applyFromArray($alignCenterTopBottomRightLeftThin);
                $countMawbDataLine++;
            }
        }
    }
    $countMawbDataLine = $countMawbDataLine+1;
    $cssCount = $countMawbDataLine+1;
    $objPHPExcel->getActiveSheet()->getStyle('B'.$countMawbDataLine.':E'.$cssCount)->applyFromArray($alignCenterTopBottomRightLeftBold);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$countMawbDataLine, 'Totals:');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$countMawbDataLine, 'Shipments');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$countMawbDataLine, 'Total Weight');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$countMawbDataLine, 'Value');
    $countMawbDataLine = $countMawbDataLine+1;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$countMawbDataLine, '');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$countMawbDataLine, $totalParcel);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$countMawbDataLine, $totalWeight);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$countMawbDataLine, $totalValue);

    $folder_path = "../_assets/export_manifest/";
    if (!file_exists($folder_path)) {
        mkdir($folder_path, 0777, true);
    }
    // Rename sheet
    $objPHPExcel->getActiveSheet()->setTitle('Bag Manifest');
    $fileName = "Bag-Manifest-" . strtoupper($bagging->getBagValue())."-".time(). rand('1', '100000') . ".xlsx";
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    //$objPHPExcel->setActiveSheetIndex(0);
    // Redirect output to a clientï¿½s web browser (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename='.$fileName.'');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    ob_clean();
    $objWriter->save($folder_path.$fileName);
    $bagging->setBagManifest(SETTING_MAIN_ASSETS."export_manifest/".$fileName);
    $bagging->save();
    return SETTING_MAIN_ASSETS."export_manifest/".$fileName;
}
function getExcelFileValueBased($flightList,$HLValue="lv") {
    $fileName="Manifest_".strtoupper($HLValue);

    //date_default_timezone_set('Europe/London');
    /** PHPExcel */
    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
    $excel_column = 1;
    // Design array here
    //
    $lastHeader = array(
        'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THICK,
            )
        ),
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 10,
            'name' => 'Calibri',
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'F2F2F2')
        ),
    );
    $bold = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 10,
            'name' => 'Calibri',
        )
    );
    $boldAllignCenter = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => '000000'),
            'size' => 10,
            'name' => 'Calibri',
        )
    );
    $lastTable = array(
        'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'borders' => array(
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        ),
        'font' => array(
            'color' => array('rgb' => '000000'),
            'size' => 9,
            'name' => 'Calibri',
        ),
    );
    // Set properties
    $objPHPExcel->getProperties()->setCreator("One World Express ")
        ->setLastModifiedBy("Operation Syste,")
        ->setTitle("Office 2007 XLSX Sales Invoice")
        ->setSubject("Office 2007 XLSX Sales Invoice")
        ->setDescription("This document is generated from the system, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Sales Invoice");
    // Add some data
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('E1', 'Manifest')->mergeCells('E1:H1')
        ->setCellValue('A3', 'From:'.$flightList['flight_from_country'])->mergeCells('A3:B3')
        ->setCellValue('D3', 'To: '.$flightList['flight_to_country'])->mergeCells('D3:E3')
        ->setCellValue('F3', 'MAWB:'.$flightList['flight_'.$HLValue.'_data'][0]['mawb'])->mergeCells('F3:G3')
        ->setCellValue('A4', 'Create Date:'.Date("d-m-Y"))->mergeCells('A4:B4')
        ->setCellValue('A5', 'Total Pieces:'.$flightList['flight_total_parcel_'.$HLValue])->mergeCells('A5:B5')
        ->setCellValue('E5', 'Total Weight:'.$flightList['flight_total_parcel_weight_'.$HLValue])->mergeCells('E5:F5');
    $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($boldAllignCenter);
    $objPHPExcel->getActiveSheet()->getStyle('A3:B3')->applyFromArray($boldAllignCenter);
    $objPHPExcel->getActiveSheet()->getStyle('D3:E3')->applyFromArray($boldAllignCenter);
    $objPHPExcel->getActiveSheet()->getStyle('F3:G3')->applyFromArray($boldAllignCenter);
    $objPHPExcel->getActiveSheet()->getStyle('A4:B4')->applyFromArray($bold);
    $objPHPExcel->getActiveSheet()->getStyle('A5:B5')->applyFromArray($bold);
    $objPHPExcel->getActiveSheet()->getStyle('E5:F5')->applyFromArray($bold);
    $objPHPExcel->getActiveSheet()->getStyle('A7')->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('B7')->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('C7')->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('D7')->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('E7')->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('F7')->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('G7')->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('H7')->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('I7')->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('J7')->applyFromArray($lastHeader);
    $objPHPExcel->getActiveSheet()->getStyle('K7')->applyFromArray($lastHeader);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A7', 'No.')
        ->setCellValue('B7', 'HAWB')
        ->setCellValue('C7', 'Tracking No.')
        ->setCellValue('D7', 'Country')
        ->setCellValue('E7', 'Shipper Details')
        ->setCellValue('F7', "Consignee's Details")
        ->setCellValue('G7', 'No. of Pieces')
        ->setCellValue('H7', 'Weight (kg)')
        ->setCellValue('I7', 'Description')
        ->setCellValue('J7', 'Value')
        ->setCellValue('K7', 'Currency');
    //Data from flight table
    $number = 1;
    $countMawbDataLine = 7;
    $flightDataArr = [];
    if($fileName == "Manifest_LV"){
        $flightDataArr = $flightList['flight_lv_data'];
    }else{
        $flightDataArr = $flightList['flight_hv_data'];
    }
    foreach ($flightDataArr as $mawbData) {
        $countMawbDataLine++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, $number)
            ->setCellValue('B'.$countMawbDataLine, "'".$mawbData['hawb']."'")
            ->setCellValue('C'.$countMawbDataLine, $mawbData['awb'])
            ->setCellValue('D'.$countMawbDataLine, $mawbData['to_country'])
            ->setCellValue('E'.$countMawbDataLine, $mawbData['sender_name']." ".$mawbData['sender_address_line_1']." ".$mawbData['sender_address_line_2']." ".$mawbData['sender_address_line_3']." ".$mawbData['sender_city']." ".$mawbData['sender_state']." ".$mawbData['sender_postcode'])
            ->setCellValue('F'.$countMawbDataLine, $mawbData['receiver_name']." ".$mawbData['address_line_1']." ".$mawbData['address_line_2']." ".$mawbData['address_line_3']." ".$mawbData['city']." ".$mawbData['state']." ".$mawbData['postcode'])
            ->setCellValue('G'.$countMawbDataLine, $mawbData['no_of_pieces'])
            ->setCellValue('H'.$countMawbDataLine, $mawbData['weight'])
            ->setCellValue('I'.$countMawbDataLine, $mawbData['description'])
            ->setCellValue('J'.$countMawbDataLine, $mawbData['foreign_value'])
            ->setCellValue('K'.$countMawbDataLine, $mawbData['currency']);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('E'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('F'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('G'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('H'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('I'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('J'.$countMawbDataLine)->applyFromArray($lastTable);
        $objPHPExcel->getActiveSheet()->getStyle('K'.$countMawbDataLine)->applyFromArray($lastTable);
        $number++;
    }
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(17);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(17);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(17);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
    $folder_path = "../_assets/";
    if (!file_exists($folder_path."export_manifest")) {
        mkdir($folder_path, 0777, true);
    }
    // Rename sheet
    $objPHPExcel->getActiveSheet()->setTitle($fileName);
    $fileName = "export_manifest/".$fileName."-". time() . ".xlsx";
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    //$objPHPExcel->setActiveSheetIndex(0);
    // Redirect output to a clientï¿½s web browser (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename='.$fileName.'');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    ob_clean();
    $objWriter->save($folder_path.$fileName);
    return $fileName;
}
function getBagIdWithValue($parcelValue,$consignmentCurrency,$fligthToCountryId){
    $return = [];
    if($fligthToCountryId > 0){
        $countryObj = new Country($fligthToCountryId);
        $bagLowValue = $countryObj->getBagLowValue();
        $currencyId = $countryObj->getCurrencyId();
        $currencyObj = new Currency($currencyId);
        $rightSymbolFromCurrency = $currencyObj->getRightsymbol();
        $countryLowValue = Currency::convertCurrency($consignmentCurrency,$rightSymbolFromCurrency, $parcelValue);
        $return['value'] = $countryLowValue;
        $return['bag_low_value'] = $bagLowValue;
    }
    return $return;
}
// Najam Code
function mawbManifestExcel($parcelDetails) {
    $mawbNumber = $parcelDetails['mawb_number'];
    $fromAddress = $parcelDetails['flight_from_address'];
    $toAddress = $parcelDetails['flight_to_address'];
    $parcelDetails = $parcelDetails['parcel_data'];

    $styleMainHeading = array(
        'font' => array(
            'bold' => true,
            'size' => 16
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
    );
    $styleHeading = array(
        'font' => array(
            'bold' => true,
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
    );
    $styleHeadingMiddle = array(
        'font' => array(
            'bold' => true,
        ),
        'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
    );
    $styleData = array(
        'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        ),
        'borders' => array(
            'top' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'left' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        ),
    );
    $objPHPExcel = new PHPExcel();

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);

    $objPHPExcel->getActiveSheet()->mergeCells('E1:H2');
    $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleMainHeading);
    $objPHPExcel->getActiveSheet()->SetCellValue('E1', "MANIFEST");
    $objPHPExcel->getActiveSheet()->SetCellValue('A3',"From:");
    $objPHPExcel->getActiveSheet()->SetCellValue('A4',$fromAddress)->mergeCells('A4:B6');
    $objPHPExcel->getActiveSheet()->getStyle('A4')->applyFromArray($styleHeading);
    $objPHPExcel->getActiveSheet()->mergeCells('C4:C6');
    $objPHPExcel->getActiveSheet()->SetCellValue('D3',"To:");
    $objPHPExcel->getActiveSheet()->SetCellValue('D4',$toAddress)->mergeCells('D4:E6');
    $objPHPExcel->getActiveSheet()->getStyle('D4')->applyFromArray($styleHeading);
    $objPHPExcel->getActiveSheet()->getStyle('D4')->getAlignment()->setWrapText(true);
    $objPHPExcel->getActiveSheet()->mergeCells('F4:G6');
    $objPHPExcel->getActiveSheet()->getStyle('F4')->applyFromArray($styleHeading);
    $objPHPExcel->getActiveSheet()->getStyle('F4')->getAlignment()->setWrapText(true);
    $objPHPExcel->getActiveSheet()->SetCellValue('F4', "MAWB: ".$mawbNumber);
    $objPHPExcel->getActiveSheet()->mergeCells('A7:B7');
    $objPHPExcel->getActiveSheet()->getStyle('A7')->applyFromArray($styleHeadingMiddle);
    $objPHPExcel->getActiveSheet()->SetCellValue('A7', "Create Date:".Date("d-m-Y"));
    $objPHPExcel->getActiveSheet()->mergeCells('A8:B8');
    $objPHPExcel->getActiveSheet()->getStyle('A8')->applyFromArray($styleHeadingMiddle);
    $objPHPExcel->getActiveSheet()->SetCellValue('A8', "Total Pieces: ".count($parcelDetails));
    $objPHPExcel->getActiveSheet()->getStyle('E8')->applyFromArray($styleHeadingMiddle);


    $heading = [
        "No.",
        "HAWB",
        "Tracking No.",
        "Country",
        "Shipper Details",
        "Consignee's Details",
        "Contact Number",
        "No. of Pieces",
        "Weight (kg)",
        "Description",
        "Value",
        "Currency",
        "Bag No",
        "Note",
    ];
    $rowNum=10;
    $colNum='A';
    foreach($heading as $value) {
        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $value);
        $colNum++;
    }
    $rowNum++;
    $curCount = 1;
    $parcelWeight = 0;
    foreach ($parcelDetails as $key => $DataArr) {
        if($DataArr[6] > 0)
            $parcelWeight += $DataArr[6];
        $colNum='A';
        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $curCount);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $DataArr[0]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $DataArr[1]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $DataArr[2]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $DataArr[3]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $DataArr[4]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $DataArr[11]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $DataArr[5]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $DataArr[6]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $DataArr[7]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $DataArr[8]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $DataArr[9]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $DataArr[10]);
        $colNum++;
        $objPHPExcel->getActiveSheet()->getStyle($colNum.$rowNum)->applyFromArray($styleData);
        $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $DataArr[12]);
        $rowNum++;
        $curCount++;
    }
    $objPHPExcel->getActiveSheet()->SetCellValue('E8', "Total Weight: ".$parcelWeight." KG");
    $folder_path = "../_assets/export_manifest/";
    if (!file_exists($folder_path)) {
        mkdir($folder_path, 0777, true);
    }
    // Rename sheet
    $objPHPExcel->getActiveSheet()->setTitle('MAWB Manifest');
    $fileName = "MAWB-Manifest-" . time() . rand(1,1000).".xlsx";
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    //$objPHPExcel->setActiveSheetIndex(0);
    // Redirect output to a clientï¿½s web browser (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename='.$fileName.'');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    ob_clean();
    $objWriter->save($folder_path.$fileName);
    return "export_manifest/".$fileName;
}
// End najam Code
function getLvHvDataMawb($mawbId,$flightId,$bagType){
    // Make Data
    $mawb = new Mawb($mawbId);
    $mawbNumber = $mawb->getMawbNumber();
    $flightData = new FlightInfo($flightId);
    $mawbParcelMappingFilter = new MawbParcelMappingFilter();
    $mawbParcelMappingFilter->addJoin('bagging bag',"bag.id","mpm.bag_id");
    $mawbParcelMappingFilter->addFieldFilter("    mpm.mawb_id", $mawbId);
    $mawbParcelMappingFilter->addFieldFilter("    bag.bag_value", $bagType);
    $mawbParcelMappingFilter->addGroupBy("mpm.bag_id");
    $mawbParcelMappingObj = $mawbParcelMappingFilter->getList();
    $mawbBagCount = 0;
    $bagParcelWeight = 0;
    $parcelDetails = [];
    $shipperCountry = "";
    $recieverCountry = "";
    $shipperCountryName = "";
    $recieverCountryName = "";

    $shipperCountry = new Country($flightData->getCountryId());
    $shipperCountryName = $shipperCountry->getName();

    $recieverCountry = new Country($flightData->getDestinationCountryId());
    $recieverCountryName = $recieverCountry->getName();

    $parcelDetails['mawb_number'] = $mawbNumber;
    $parcelDetails['flight_from_address'] = $flightData->getShipperCo().' '.$flightData->getShippersAddressline1().' '.$flightData->getShippersAddressline2().' '.$shipperCountryName;

    $parcelDetails['flight_to_address'] = $flightData->getConsigneeCo().' '.$flightData->getAddressLine1().' '.$flightData->getAddressLine2().' '.$recieverCountryName;
    if(count($mawbParcelMappingObj) > 0){
        $mawbBagCount = count($mawbParcelMappingObj);
        foreach ($mawbParcelMappingObj as $mawbParcelMappingArr) {
            $parcelBaggingMappingFilter = new ParcelBaggingMappingFilter();
            $parcelBaggingMappingFilter->addFieldFilter("    bag_id", $mawbParcelMappingArr->getBagId());
            $parcelBaggingMappingObj = $parcelBaggingMappingFilter->getList();
            $bagging = new Bagging($mawbParcelMappingArr->getBagId());
            //if($bagging->getBagValue() == $bagType){
            if(count($parcelBaggingMappingObj) > 0){
                $MawbBagParcelCount = count($parcelBaggingMappingObj);
                $shipper = "";
                $reciever=  "";
                foreach ($parcelBaggingMappingObj as $parcelBaggingMappingArr) {
                    $parcel = new parcel($parcelBaggingMappingArr->getParcelId());
                    $bagParcelWeight = $bagParcelWeight + $parcel->getWeight();
                    $consignment = new Consignment($parcel->getConsignmentId());
                    $shipperCountryObj = new Country($consignment->getSenderCountryId());
                    $recCountryObj = new Country($consignment->getCountryId());
                    $recCountryName = $recCountryObj->getName();
                    $shipperCountryName = $shipperCountryObj->getName();


                    $shipper = $consignment->getSenderName()." ".$consignment->getSenderCompany()." ".$consignment->getSenderAddressLine1()." ".$consignment->getSenderAddressLine2()." ".$consignment->getSenderAddressLine3()." ".$consignment->getSenderCity()." ".$consignment->getSenderPostcode()." ".$shipperCountryName;

                    $reciever = $consignment->getContact()." ".$consignment->getCompany()." ".$consignment->getAddressLine1()." ".$consignment->getAddressLine2()." ".$consignment->getAddressLine3()." ".$consignment->getCity()." ".$consignment->getPostcode()." ".$recCountryName;

                    $parcelDetails['parcel_data'][$parcel->getId()] = [
                        $consignment->getHawb(),
                        $parcel->getTrackingNumber(),
                        $recCountryName,
                        $shipper,
                        $reciever,
                        '1',
                        $parcel->getWeight(),
                        $consignment->getDescription(),
                        $parcel->getItemvalue(),
                        $consignment->getCurrency(),
                        $bagging->getBagnumber(),
                        $consignment->getTelephone(),
                        $consignment->getNotes()
                    ];
                }
            }
            //}
        }
    }
    return $parcelDetails;
}
?>
