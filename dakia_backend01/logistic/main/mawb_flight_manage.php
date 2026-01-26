<?php
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../includes/3rdparty/phpexcel/PHPExcel.php");
//require_once("../includes/mapping/glorderpdf8.class.php");
include_classes([
            'pdfmerger','ocbaglabel.class'
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
    'warehouse.class',
    'tracking.class',
    'trackingdata.class',
    'trackingdatafilter.class'
]);


class Page extends BasePage {

    /*     * *
     * Controller logic
     */
    private $mawbTypeArray = array (''=>'Smart Track','CpostIps'=>'C Post Ips');
    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "MAWB Dispatch Manage"
        );
        $user = SessionManager::getUser();
        /*
         * DataTable handlings
         */
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "get_mawb") {
            $returnHtml = '<option value="">Please select Mawb</option>';
            $flightId = trim($this->form_vars['flight_id']);
            if ($flightId > 0) {
                $flightInfoObj = new FlightInfo($flightId);
                if (count($flightInfoObj) > 0) {
                    $mawbFilter = new MawbFilter();
                    $mawbObj = $mawbFilter->getFlightMawbList($flightId,$flightInfoObj->getCountryId(), $flightInfoObj->getDestinationCountryId());
                    if (count($mawbObj) > 0) {
                        foreach ($mawbObj as $mawbArr) {
                            $returnHtml .= '<option value="' . $mawbArr->getId() . '">' . $mawbArr->getMawbNumber() . '</option>';
                        }
                    }
                }
            }
            echo $returnHtml;
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "create_mawb") {
            $flightId = trim($this->form_vars['flight_id']);
            $mawbNumber = trim($this->form_vars['mawb_number']);
            $destinationCountryId = trim($this->form_vars['destination_country_id']);
            $destinationWarehouseId = trim($this->form_vars['destination_warehouse_id']);

            if ($flightId > 0) {
                $flightInfoObj = new FlightInfo($flightId);
                if (count($flightInfoObj) > 0) {
                    $sourceCountryId = $flightInfoObj->getCountryId();
                    $destinationCountryId = $flightInfoObj->getDestinationCountryId();
                }
            }
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "add_tracking") {
            /*
             * Check if flight is open
             * Check if mawb is open (partial dispatch and open)
             * Check if bag is open
             */
            $user = SessionManager::getUser();
            $flightId = trim($this->form_vars['flight_id']);
            $trackingNumber = trim($this->form_vars['tracking_number']);
            $mawbId = trim($this->form_vars['mawb_id']);
            $returnData = [];
            if ($flightId <= 0) {
                $returnData['status'] = "error";
                $returnData['message'] = "Select flight";
            }
            if ($mawbId <= 0) {
                $returnData['status'] = "error";
                $returnData['message'] = "Select MAWB";
            }
            if (empty($trackingNumber)) {
                $returnData['status'] = "error";
                $returnData['message'] = "Enter Tracking Number";
            }
            if (empty($returnData)) {
                // Check if parcel exsist/ Get parcel service
                $parcel = new ParcelFilter();
                $parcelObj = $parcel->getParcelSerivce($trackingNumber);
                if (count($parcelObj) == 0) {
                    // Check if its bag number
                    $isBag = false;
                    $baggingFilter = New BaggingFilter();
                    $baggingFilter->addFieldFilter("    bagnumber",$trackingNumber);
                    $baggingFilter->addFieldFilter("    is_country_bagging","y");
                    $baggingObj = $baggingFilter->getColumnList("*");
                    if(count($baggingObj) > 0){
                        $isBag = true;
                    }
                }
                if (count($parcelObj) > 0 || $isBag) {
                    // Check flight is open
                    $flightInfoFilter = new FlighInfoFilter();
                    $flightInfoFilter->addFieldFilter('    id', $flightId);
                    $flightInfoFilter->addFieldFilter('is_delete', '0');
                    $flightInfoFilter->addFieldFilter('is_closed', '0');
                    $flightInfoObj = $flightInfoFilter->getColumnList("flight_number,country_id,destination_country_id,transport_type");
                    if (count($flightInfoObj) > 0) {
                        $sourceCountryId = $flightInfoObj[0]->getCountryId();
                        $destinationCountryId = $flightInfoObj[0]->getDestinationCountryId();
                        $transportType = $flightInfoObj[0]->getTransportType();
                        // Check if mawb open
                        $mawbFilter = new MawbFilter();
                        $mawbFilter->addFieldFilter("    id", $mawbId);
                        $mawbFilter->addFieldFilter("mawb_status", "o");
                        $mawbFilter->addOrFilter("mawb_status", "pd");
                        $mawbObj = $mawbFilter->getList();
                        if (count($mawbObj)) {
                            if($isBag){
                                $isLowValBag = $baggingObj[0]->getBagValue();
                                $parcelBaggingMappingFilter = New ParcelBaggingMappingFilter();
                                $parcelBaggingMappingFilter->addFieldFilter("    bag_id", $baggingObj[0]->getId());
                                $countryParcelBaggingMappingObj = $parcelBaggingMappingFilter->getColumnList("*");
                                if(count($countryParcelBaggingMappingObj) > 0){
                                    foreach ($countryParcelBaggingMappingObj as $countryParcelBaggingMappingArr) {
                                        // Add data into mawb parcel mapping
                                        $mawbParcelMapping = new MawbParcelMapping();
                                        $mawbParcelMapping->setMawbId($mawbId);
                                        $mawbParcelMapping->setParcelId($countryParcelBaggingMappingArr->getParcelId());
                                        $mawbParcelMapping->setBagId($baggingObj[0]->getId());
                                        $mawbParcelMapping->setDateAdded(time());
                                        $mawbParcelMapping->setAddedBy($user->getId());
                                        $mawbParcelMapping->save();
                                        // check if data already added into
                                        $flightinfoMappingFilter = new FlightMappingFilter();
                                        $flightinfoMappingFilter->addFieldFilter("    flight_info_id", $flightId);
                                        $flightinfoMappingFilter->addFieldFilter("    mawb_id", $mawbId);
                                        $flightinfoMappingObj = $flightinfoMappingFilter->getColumnList("id,mawb_id,is_delete");
                                        if (count($flightinfoMappingObj) > 0) {
                                            $flightinfoMappingObj[0]->getIsDelete();
                                            if($flightinfoMappingObj[0]->getIsDelete() == 1){
                                                $flightinfoMapping = new FlightMapping($flightinfoMappingObj[0]->getId());
                                                $flightinfoMapping->setIsDelete(0);
                                                $flightinfoMapping->save();
                                            }
                                        } else {
                                            $flightinfoMapping = new FlightMapping();
                                            $flightinfoMapping->setFlightInfoId($flightId);
                                            $flightinfoMapping->setMawbId($mawbId);
                                            $flightinfoMapping->setIsDelete(0);
                                            $flightinfoMapping->save();
                                        }
                                    }
                                    $baggingNewObj = New Bagging($baggingObj[0]->getId());
                                    $baggingNewObj->setIsCountryBagging("n");
                                    $baggingNewObj->save();
                                    $returnData['status'] = "success";
                                    $returnData['message'] = '<span style="font-size: 30px;color: black;">'.$isLowValBag.'</span><br />Bag Number ['.$trackingNumber.'] ';
                                }
                            }else {
                                // paremetters are getBagIdWithValue($parcelValue,$consignmentCurrency,$fligthToCountryId)
                                $bagValue = getBagIdWithValue($parcelObj[0]->getItemvalue(), $parcelObj[0]->getChuteSorted(), $destinationCountryId);
                                $bagLowValue = 0;
                                if ($transportType == "flight") {
                                    $isLowValBag = 'lv';
                                    if (!empty($bagValue['value'])) {
                                        if ($bagValue['value'] >= $bagValue['bag_low_value'])
                                            $isLowValBag = 'hv';
                                        else
                                            $isLowValBag = 'lv';
                                    }

                                } else {
                                    $isLowValBag = "";
                                }
                                // Check if parcel is already added into bag
                                $mawbParcelMappingFilter = new MawbParcelMappingFilter();
                                $mawbParcelMappingFilter->addFieldFilter("    mawb_id", $mawbId);
                                $mawbParcelMappingFilter->addFieldFilter("    parcel_id", $parcelObj[0]->getId());
                                $mawbParcelBagFoundObj = $mawbParcelMappingFilter->getColumnList("bag_id");
                                if (count($mawbParcelBagFoundObj) > 0) {
                                    $baggingNew = new Bagging($mawbParcelBagFoundObj[0]->getBagId());
                                    $returnData['status'] = "error";
                                    $returnData['message'] = "Parcel is already added into bag [" . $baggingNew->getBagnumber() . "]";
                                    echo json_encode($returnData);
                                    die;
                                }
                                // Check if same service open bag exsit and weight limit is less then country weight limit
                                $bagging = new BaggingFilter();
                                $baggingObj = $bagging->getServiceBag($parcelObj[0]->getQty(), $mawbId, $isLowValBag);
                                if (count($baggingObj) > 0) {
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
                                        $returnData = addDataIntoMappingTable($baggingObj[0]->getId(), $mawbId, $parcelObj[0]->getId(), $flightId, $baggingObj[0]->getBagnumber(), $isLowValBag, $trackingNumber);
                                    } else {
                                        // Bag is opened and weight limit is over
                                        $returnData['status'] = "error";
                                        $returnData['message'] = "Bag weight limit [" . $countryBagWeightLimit . "] exceeded. Please close the bag [" . $baggingObj[0]->getBagnumber() . "]";
                                    }
                                } else {
                                    // No bag is opened with this service
                                    $bagdata = createBagWithServiceId($parcelObj[0]->getQty(), $sourceCountryId, $destinationCountryId, $isLowValBag);
                                    $returnDataJson = json_decode($bagdata, true);
                                    $returnData = addDataIntoMappingTable($returnDataJson['bag_id'], $mawbId, $parcelObj[0]->getId(), $flightId, $returnDataJson['bag_number'], $isLowValBag, $trackingNumber);
                                }
                            }
                        } else {
                            // mawb is not open
                            $returnData['status'] = "error";
                            $returnData['message'] = "MAWB is closed";
                        }
                    } else {
                        // Flight is not open
                        $returnData['status'] = "error";
                        $returnData['message'] = "Flight is closed";
                    }
                } else {

                    // Parcel not found or service not found
                    $returnData['status'] = "error";
                    $returnData['message'] = "tracking not found";
                }
            }
            echo json_encode($returnData);
            die;
        }
        /// Mariya Task start
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "get_flight_mawb_stats") {
            $flightId = trim($this->form_vars['flight_id']);
            if ($flightId <= 0) {
                $returnData['status'] = "error";
                $returnData['message'] = "Flight Number Empty";
            }
            if (empty($returnData)) {
                // Check if parcel exsist/ Get parcel service
                $dataArray = [];
                $flightInfo = new FlightInfo($flightId);
                if($flightInfo->getTransportType() == "flight"){
                    $flight = New Flight($flightInfo->getTransportId());
                    $dataArray['flight_info']['flight_number'] = $flight->getFlightNumber();
                }else if($flightInfo->getTransportType() == "sea"){
                    $sea = New Sea($flightInfo->getTransportId());
                    $dataArray['flight_info']['flight_number'] = $sea->getShipNumber();
                }else{
                    $truck = New Truck($flightInfo->getTransportId());
                    $dataArray['flight_info']['flight_number'] = $truck->getTruckNumber();
                }
                $dataArray['flight_info']['flight_id'] = $flightId;
                $dataArray['flight_info']['arrival_date'] = date('d-m-Y H:i:s', strtotime($flightInfo->getEta()));
                $dataArray['flight_info']['departure_date'] = date('d-m-Y H:i:s', strtotime($flightInfo->getEtd()));
                $dataArray['flight_info']['from_country'] = $flightInfo->getCountryId();
                $dataArray['flight_info']['to_country'] = $flightInfo->getDestinationCountryId();
                $dataArray['mawb_info'] = [];
                $flightMapping = new FlightMapping();
                $flightMawb = $flightMapping->getFlightMawbList($flightId);
                $flightMawbs = [];
                if (!empty($flightMawb)) {
                    foreach ($flightMawb as $mawb) {
                        $flightMawbs[] = $mawb->getMawbId();
                    }
                }
                if (!empty($flightMawbs)) {
                    $mawbs = new MawbFilter();
                    $mawbs->addFilter(' id IN (' . implode(',', $flightMawbs) . ')');
                    $mawbs->AddOrderBy('id',FALSE);
                    $mawbs = $mawbs->getList();
                    $i = 0;
                    foreach ($mawbs as $mawb) {
                        $dataArray['mawb_info'][$i] = [
                            'mawb_number' => $mawb->getMawbNumber(),
                            'mawb_id' => $mawb->getId(),
                            'mawb_status' => $mawb->getMawbStatus(),
                            'mawb_manifest_lv' => $mawb->getMawbLvManifest(),
                            'mawb_manifest_hv' => $mawb->getMawbHvManifest()
                        ];
                        $mawbBags = new MawbParcelMappingFilter();
                        $mawbBags->addFilter(' mawb_id = ' . $mawb->getId());
                        $mawbBags->addFilter(' bag_id > 0 ');
                        $mawbBags->addGroupBy('bag_id');
                        $mawbBags = $mawbBags->getList();
//                        $totalBag = count($mawb_info['bags_info']);
                        $openBags = 0;
                        if (!empty($mawbBags)) {
                            foreach ($mawbBags as $mawbBag) {
                                $bagging = new Bagging($mawbBag->getBagId());
                                if($bagging->getIsClosed() == 0){
                                    $openBags++;
                                }
                                $baggingObj = BaggingFilter::getParcelTotalFromBagId($mawbBag->getBagId());
                                $baggingParcelCount = 0;
                                if (count($baggingObj) > 0) {
                                    $baggingParcelCount = $baggingObj[0]->getId();
                                }
                                $dataArray['mawb_info'][$i]['bags_info'][] = [
                                    'bag_id' => $bagging->getId(),
                                    'bag_number' => $bagging->getBagnumber(),
                                    'total_parcels' => $baggingParcelCount,
                                    'service_id' => $bagging->getService(),
                                    'is_closed' => $bagging->getIsClosed(),
                                    'weight' => $bagging->getWeight(),
                                    'bag_manifest' => $bagging->getBagManifest(),
                                    'bag_label' => $bagging->getBagLabel(),
                                    'is_bag_open' => $isAnyBagOpen,
                                    'bag_value' => $bagging->getBagValue(),
                                    'oc_bag' => $bagging->getOcBagLabel()
                                ];
                            }
                            $dataArray['mawb_info'][$i]['open_bags'] = $openBags;
                        }
                        $i++;
                    }
                }
//                echo "<pre>";
//                print_r($dataArray);
//                die;
                $toCountry = new Country($dataArray['flight_info']['to_country']);
                $toCountryName = $toCountry->getName();
                $toCountryIso = $toCountry->getIso();
                $fromCountry = new Country($dataArray['flight_info']['from_country']);
                $fromCountry = $fromCountry->getName();
                $html = '';
                $htmlCloseFligthBtn = "";
//                echo '<pre>';
//                print_r($flightInfo->getIsClosed());
//                echo '</pre>';
//                die;
                if ($flightInfo->getIsClosed() == 0) {
                    $htmlCloseFligthBtn = '<span style="cursor: pointer;" data-flight_id="' . $flightId . '" class="btn blue btn-lg close_flight_btn">Close Flight</span>';
                }else{
                    $SAManifestFileLv = $flightInfo->getFilesLv();
                    $SAManifestFileHv = $flightInfo->getFilesHv();
                    $lowValueExcel = $flightInfo->getLowValueManifest();
                    $highValueExcel = $flightInfo->getHighValueManifest();
                    $invoicesLink = $flightInfo->getInvoice();
                    $htmlCloseFligthBtn = "";
                    if(!empty($SAManifestFileLv))
                        $htmlCloseFligthBtn .= '<a href="'.SETTING_MAIN_ASSETS.$SAManifestFileLv.'" class="btn btn-default btn-xs margin-right-10 margin-bottom-10" data-toggle="tooltip" title="Download LV Manifest"><i class="fa fa-download"></i> '.$toCountryIso.' LV Manifest</a>';
                    if(!empty($SAManifestFileHv))
                        $htmlCloseFligthBtn .= '<a href="'.SETTING_MAIN_ASSETS.$SAManifestFileHv.'" class="btn btn-default btn-xs margin-right-10 margin-bottom-10" data-toggle="tooltip" title="Download HV Manifest"><i class="fa fa-download"></i> '.$toCountryIso.' HV Manifest</a>';
                    
                    if(!empty($lowValueExcel))
                        $htmlCloseFligthBtn .= '<a href="'.SETTING_MAIN_ASSETS.$lowValueExcel.'" class="btn btn-default btn-xs margin-right-10 margin-bottom-10" data-toggle="tooltip" title="Download LV Manifest"><i class="fa fa-download"></i> LV Manifest</a>';
                    
                    if(!empty($highValueExcel))
                        $htmlCloseFligthBtn .= '<a href="'.SETTING_MAIN_ASSETS.$highValueExcel.'" class="btn btn-default btn-xs margin-right-10 margin-bottom-10" data-toggle="tooltip" title="Download HV Manifest"><i class="fa fa-download"></i> HV Manifest</a>';
                    
                    if(!empty($invoicesLink))
                        $htmlCloseFligthBtn .= '<a href="'.SETTING_MAIN_ASSETS.$invoicesLink.'" class="btn btn-default btn-xs margin-right-10 margin-bottom-10" target="_blank" data-toggle="tooltip" title="Download Invoice"><i class="fa fa-download"></i> Invoice</a>';
                    
                    
                }
                $html .= '<div class="row">
                                <div class="col-md-6">
                                    <div class="col-md-6">
                                            <b>Departure From</b>
                                            <p>' . $fromCountry . '</p>
                                    </div>
                                    <div class="col-md-6">
                                            <b>Arrival To</b>
                                            <p id="p_destination_country_id" data-country_id="'.$toCountry->getId().'">' . $toCountryName . '</p>
                                    </div>
                                    <div class="col-md-6">
                                            <b>Arrival Date</b>
                                            <p>' . $dataArray['flight_info']['arrival_date'] . '</p>
                                    </div>
                                    <div class="col-md-6">
                                            <b>Departure Date</b>
                                            <p>' . $dataArray['flight_info']['departure_date'] . '</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="col-md-12">
                                            ' . $htmlCloseFligthBtn . '
                                    </div>
                                    <div class="col-md-12">
                                        <label>Add Track Point</label>
                                        <div class="form-group">
                                            <div class="md-radio-inline">
                                                <input name="add_track_point" type="checkbox" class="make-switch"  data-on-text="Yes" check data-off-text="No" data-on-color="primary" data-off-color="danger" id="add_track_point">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ';
                $html .= '<div class="clearfix"></div>';
                if (!empty($dataArray['mawb_info'])) {
                    foreach ($dataArray['mawb_info'] as $mawb_info) {
                        $i = 1;
                        $openBags = $mawb_info['open_bags'];
                        $htmlBtn = "";
                        if ($mawb_info['mawb_status'] == "o") {
                            $htmlBtn .= '<span id="mawb_id_' . $mawb_info['mawb_number'] . '"> <span style="cursor: pointer;" class="label label-sm  label-primary pull-right total_mawb_bag_count close_mawb" data-bag_count="' . $mawbTotalBagCount . '" id="' . $mawb_info['mawb_number'] . '" data-mawb_id="' . $mawb_info['mawb_id'] . '">Dispatch</span><span class="margin-right-10 label label-sm  label-primary pull-right mawb_partial_dispatch" data-bag_count="' . $mawbTotalBagCount . '" id="' . $mawb_info['mawb_number'] . '" data-mawb_id="' . $mawb_info['mawb_id'] . '" style="cursor: pointer;">Partial Dispatch</span></span>';
                        } else {
                            if ($mawb_info['mawb_status'] == "d") {
                                $htmlBtn = '<span class="label label-sm pull-right label-warning">Dispatched</span>&nbsp;&nbsp;&nbsp;<span style="cursor: pointer;margin-right: 5px;" class="label label-sm  label-primary pull-right pre_alert_mawb" data-bag_count="' . $mawbTotalBagCount . '" id="' . $mawb_info['mawb_number'] . '" data-flight_id="' . $flightId . '" data-mawb_id="' . $mawb_info['mawb_id'] . '">Pre alert</span>';
                            } else if ($mawb_info['mawb_status'] == "pd") {
                                $htmlBtn = '<span class="label label-sm pull-right label-warning">Partial Dispatched</span>';
                            }
                        }
                        if($openBags > 0){
                            $htmlBtn .= '<span data-mawb_id="'.$mawb_info['mawb_id'].'" class="label label-sm pull-right label-primary close_bags margin-right-10" style="cursor: pointer;">Close all Bag</span>';
                        }else{
                            if(!empty($mawb_info['mawb_manifest_lv'])){
                                $htmlBtn .= '<a href="'.SETTING_MAIN_ASSETS.$mawb_info['mawb_manifest_lv'].'" title="Download LV Manifest" data-toggle="tooltip" class="margin-right-10 pull-right"><i class="fa fa-file-excel-o"></i></a>';
                                
                            }
                            if(!empty($mawb_info['mawb_manifest_hv'])){
                                $htmlBtn .= '<a href="'.SETTING_MAIN_ASSETS.$mawb_info['mawb_manifest_hv'].'" title="Download HV Manifest" data-toggle="tooltip" class="margin-right-10 pull-right"><i class="fa fa-file-excel-o"></i></a>';
                                
                            }
                        }
                        $html .= '<div class="panel panel-default">
                                            <!-- Default panel contents -->
                                            <div class="panel-heading">
                                                <h3 class="panel-title">MAWB#:&nbsp;' . $mawb_info['mawb_number'] . $htmlBtn . '</h3>
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
                        foreach ($mawb_info['bags_info'] as $bag_info) {
                            $mawbNumberCls = "";
                            if ($bag_info['is_closed'] == 0) {
                                $labelClass = "label-success";
                                $label = "Open";
                                $button = "<a href='javascript:void(0);' data-mawb_id='".$mawb_info['mawb_id']."' data-bag_id='" . $bag_info['bag_id'] . "' class='btn btn-primary btn-sm close_bag'>Close</a>";
                            } else {
                                $closedBag++;
                                $mawbNumberCls = $mawb_info['mawb_number'];
                                $labelClass = "label-danger";
                                $label = "Closed";
                                $button = '';
                                if ($mawb_info['mawb_status'] == "o")
                                    $button .= '<a href="javascript:void(0);" data-bag_id="' . $bag_info['bag_id'] . '" class="margin-right-10 reopen_bag" data-toggle="tooltip" title="Re-Open"><i class="fa fa-retweet"></i></a>';
                                $button .= '<a href="'.$bag_info['bag_label'].'"  class="margin-right-10" data-toggle="tooltip" target="_blank" title="Download Label"><i class="fa fa-file-pdf-o"></i></a>';
                                $button .= '<a href="'.$bag_info['bag_manifest'].'" class="margin-right-10" data-toggle="tooltip" title="Download Manifest"><i class="fa fa-file-excel-o "></i></a>';
                                if($bag_info['oc_bag'] == "") {
                                    $button .= '<span style="cursor: pointer;margin-right: 5px;" class="label label-sm oc_bag_label label-primary pull-right" data-bag_id="' . $bag_info['bag_id'] . '" data-mawb_number="' . $mawb_info['mawb_number'] . '" >OC Label</span>';
                                }else{
                                    $button .= '<a href="'.$bag_info['oc_bag'].'"  class="margin-right-10" data-toggle="tooltip" target="_blank" title="Download OC Label"><i class="fa fa-file-pdf-o"></i></a>';
                                }
                            }
                            $service = new Services($bag_info['service_id']);
                            $bagValueCls = 'label-success';
                            if($bag_info['bag_value'] == "hv"){
                                $bagValueCls = 'label-info';
                            }
                            $html .= '
                                        <tr>
                                            <td class="text-center"> ' . $i . ' </td>
                                            <td>' . $bag_info['bag_number'].' <span class="label label-sm  '.$bagValueCls.'">'. strtoupper($bag_info['bag_value']).'</span> '. '<br /><small></small><i>' . $service->getName() . '</i><small></td>
                                            <td style="cursor: pointer;" class="text-center" onclick="get_parcel_details(\'' . $bag_info['bag_id'] . '\')">' . $bag_info['weight'] . ' Kg <br /><small><i><strong>Parcels:</strong> ' . $bag_info['total_parcels'] . '</i></small></td>
                                            <td class="text-center"> <span class="label label-sm ' . $mawbNumberCls . ' ' . $labelClass . '"> ' . $label . ' </span> </td>
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
                $returnData['flight_number'] = $dataArray['flight_info']['flight_number'];
                echo json_encode($returnData);
                die;
            } else {
                echo json_encode($returnData);
                die;
            }
            die;
        }
        /// Mariya task end
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "close_bag") {
            $user = SessionManager::getUser();
            $returnData = [];
            $returnData['status'] = "error";
            $returnData['message'] = "Bag can not be closed";
            $bagId = $this->form_vars['bag_id'];
            $mawbId = trim($this->form_vars['mawb_id']);
            $bagNumber = '';
            if ($bagId > 0) {
                // Check if bag is associated with that MAWB which has a class feild then make a object and call resp function
                $mawb = new Mawb($mawbId);
                $mawbClass = $mawb->getMawbClass();
                if(isset($mawbClass) && !empty($mawbClass)){
                    include_classes([
                        strtolower($mawbClass) . '.class'
                    ],'labels');
                    $consignmentList = new ParcelBaggingMappingFilter();
                    $consignmentList->addJoin('parcel p', 'p.id', 'pbm.parcel_id', 'INNER JOIN');
                    $consignmentList->addJoin('consignment c', 'c.id', 'p.consignment_id', 'INNER JOIN');
                    $consignmentList->addFieldFilter('     pbm.bag_id', $bagId);
                    $consignmentList = $consignmentList->getList('p.weight, p.tracking_number, c.sender_country_id, c.sender_postcode, c.sender_city, c.sender_address_line_1, c.sender_country_id, c.country_id, c.currency, c.value, c.contact, c.address_line_1, c.city, c.postcode, c.sender_name, c.sender_telephone, c.telephone');
                    $serivceClass = new $mawbClass();
                    // Pass Consignment List
                    $serviceResponse = $serivceClass->addBagItems($consignmentList, $mawb->getMawbNumber());
                    if($serviceResponse['STATUS'] == 'SUCCESS') {
                        $bagNumber = $serviceResponse['BAG_NUMBER'];
                    } else {
                        $returnData['status'] = "error";
                        $returnData['message'] = $serviceResponse['MESSAGE'];
                        echo json_encode($returnData);
                        die;
                    }
                }
                $bagging = new Bagging($bagId);
                $bagging->setIsClosed(1);
                $bagging->setClosedBy($user->getId());
                $bagging->setClosedDate(time());
                if(!empty($bagNumber) && !empty($mawbClass)) {
                    $bagging->setBagNumber($bagNumber);
                }
                $bagging->save();
                $label = generateBagLabel($bagId);
                $bagManifestExcel = getBagManifestExcel($bagId);
                $returnData['status'] = "success";
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
                // Check if mawb or flight is closed
                $mawbParcelMappingFilter = new MawbParcelMappingFilter();
                $mawbParcelMappingFilter->addFieldFilter("    bag_id", $bagId);
                $mawbParcelMappingObj = $mawbParcelMappingFilter->getColumnList("mawb_id");
                if(count($mawbParcelMappingObj) > 0){
                    $mawbId = $mawbParcelMappingObj[0]->getMawbId();
                    $mawbObj = new Mawb($mawbId);
                    if(empty($mawbObj->getMawbClass())) {
                        $mawbStatus = $mawbObj->getMawbStatus();
                        if($mawbStatus == "o"){
                            $bagging = new Bagging($bagId);
                            $bagging->setIsClosed(0);
                            $bagging->setReopenBy($user->getId());
                            $bagging->setReopenDate(time());
                            $bagging->save();
                            $returnData['status'] = "success";
                            $returnData['message'] = "Bag is re-open successfully";
                        }else{
                            $returnData['status'] = "error";
                            $returnData['message'] = "Bag can not be re-open, MAWB is dispatched";
                        }
                    } else {
                        $returnData['status'] = "error";
                        $returnData['message'] = "Bag can not be reopened.";
                    }

                }
            }
            echo json_encode($returnData);
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "close_mawb") {
            $user = SessionManager::getUser();
            $mawbId = $this->form_vars['mawb_id'];
            $dispatch = $this->form_vars['dispatch'];
            $flightId = $this->form_vars['flight_id'];
            $mawbLvData = "";
            $mawbHvData = "";
            $returnData['status'] = "error";
            $returnData['message'] = "Close all bags before closing the MAWB";

            if ($mawbId > 0) {
                // Check if all bags are closed?
                $mawbParcelMappingFilter = new MawbParcelMappingFilter();
                $mawbParcelMappingFilter->addFieldFilter("    mawb_id", $mawbId);
                $mawbParcelMappingObj = $mawbParcelMappingFilter->getColumnList("bag_id");
                $totalMawbBag = count($mawbParcelMappingObj);
                $closedBag = 0;
                if (count($mawbParcelMappingObj) > 0) {
                    foreach ($mawbParcelMappingObj as $mawbParcelMappingArr) {
                        $bagId = $mawbParcelMappingArr->getBagId();
                        $baggingObj = new Bagging($bagId);
                        if ($baggingObj->getIsClosed() == 1) {
                            $closedBag++;
                        }
                    }
                }
                if ($closedBag != $totalMawbBag) {
                    $returnData['status'] = "error";
                    $returnData['message'] = "Please close all bags";
                } else {
                    $mawb = new Mawb($mawbId);
                    $mawbLvData = getLvHvDataMawb($mawbId,$flightId,"lv");
                    $mawbHvData = getLvHvDataMawb($mawbId,$flightId,"hv");
                    
                    // Get CountryISO and warehouse for Dispatch track point.
                    $countryId = $user->getCountryId();
                    $country = new Country($countryId);
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
                    
                    if(count($mawbLvData['parcel_data']) > 0){
                        $mawbExcelFileLv = mawbManifestExcel($mawbLvData);
                        
                        // Add Dispatch Track Point if already not added
                        foreach($mawbLvData['parcel_data'] as $parcelId => $dataArr){
                            $tracking = new Tracking();
                            $tracking->saveTrackPoint($parcelId, 'parcel', date("Y-m-d H:i:s"), $dataArr[1], '126', $trackpoint, '', $carrierDesc, '', $dataArr[1]);                                        
                        }
                    }
                    if(count($mawbHvData['parcel_data']) > 0){
                        $mawbExcelFileHv = mawbManifestExcel($mawbHvData);
                        if($this->form_vars['add_track_point'] == "on") {
                            // Add Dispatch Track Point if already not added
                            foreach ($mawbHvData['parcel_data'] as $parcelId => $dataArr) {
                                $tracking = new Tracking();
                                $tracking->saveTrackPoint($parcelId, 'parcel', date("Y-m-d H:i:s"), $dataArr[1], '126', $trackpoint, '', $carrierDesc, '', $dataArr[1]);
                            }
                        }
                    }
                    $mawb->setMawbLvManifest($mawbExcelFileLv);
                    $mawb->setMawbHvManifest($mawbExcelFileHv);
                    $mawb->setMawbStatus($dispatch);

                    $mawb->save();

                    if(!empty($mawb->getMawbClass())) {
                        include_classes([
                            strtolower($mawb->getMawbClass()) . '.class'
                        ],'labels');
                        $mawbClass = $mawb->getMawbClass();
                        $serviceClassObj = new $mawbClass();
                        $serviceClassObj->closeMawb($mawb->getMawbNumber());
                    }

                    $returnData['status'] = "success";
                    $returnData['message'] = "MAWB closed successfully";
                }
                
                
                
            }
            echo json_encode($returnData);
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "close_flight") {
            $flightId = $this->form_vars['flight_id'];
            $returnData['status'] = "error";
            $returnData['message'] = "Flight can not be closed";
            $flightTotalMawbCount = 0;
            $flightClosedMawbCount = 0;
            $excelData = [];
            $excelData['mawb_list_hv'] = [];
            $excelData['mawb_list_lv'] = [];
            $excelData['flight_mawb_data'] = [];
            $excelData['flight_lv_data'] = [];
            $excelData['flight_hv_data'] = [];
            $totalParcelLv = 0;
            $totalParcelHv = 0;
            $parcelWightHv = 0;
            $parcelWightLv = 0;
            $parcelValueHv = 0;
            $parcelValueLv = 0;
            if ($flightId > 0) {
                // Check if flight all mawb is closed
                $flighInfoFilter = new FlightMappingFilter();
                $flighInfoFilter->addFieldFilter("    flight_info_id", $flightId);
                $flighInfoFilter->addGroupBy("mawb_id");
                $flighInfoFilterObj = $flighInfoFilter->getColumnList("mawb_id");
                $flightTotalMawbCount = count($flighInfoFilterObj);
                $mawbArr = [];
                $conArr = [];
                $flightinfoObj = new FlightInfo($flightId);
                foreach ($flighInfoFilterObj as $flighInfoFilterArr) {
                    $mawbArr[] = $flighInfoFilterArr->getMawbId();
                }
                $mawbFilter = new MawbFilter();
                $mawbFilter->addFilterIn("id", $mawbArr);
                $mawbFilter->addFieldNotEqualFilter("mawb_status", "o");
                $mawbResObj = $mawbFilter->getList();
                $flightClosedMawbCount = count($mawbResObj);
//                foreach ($mawbResObj as $mawbResArr) {
//                    $excelData['mawb_list'][] = $mawbResArr->getMawbNumber();
//                }
                if($flightTotalMawbCount != $flightClosedMawbCount){
                    $returnData['status'] = "error";
                    $returnData['message'] = "Please close all MAWB of flight";
                    echo json_encode($returnData);
                    die;
                }
                $fromCurrencyRightSym = "";
                $toCurrencyRightSym = "";
                $duty = 0;
                
                $toFlightCountryId = $flightinfoObj->getDestinationCountryId();
                $country = new Country($toFlightCountryId);
                $toCurrencyId = $country->getCurrencyId();
                $currencyTo = new Currency($toCurrencyId);
                $toCurrencyRightSym = $currencyTo->getRightsymbol();
                
                
                $flighInfoFilter = new FlighInfoFilter();
                $flightData = $flighInfoFilter->getFlightData($flightId);
                $convertedValueForeignValue = 0;
                foreach ($flightData as $flighInfoFilterArr) {
                    $countryOriganName = "";
                    $flightNumber = $flighInfoFilterArr->getFlightNumber();
                    if($flighInfoFilterArr->getOriginCountry() > 0){
                        $country = new Country($flighInfoFilterArr->getOriginCountry());
                        $countryOriganName = $country->getIso();
                    }
                    $fromCurrencyRightSym = $flighInfoFilterArr->getCurrency();
                    $convertedForeignValue = $flighInfoFilterArr->getForeignValue();
                    if($fromCurrencyRightSym != $toCurrencyRightSym && $flighInfoFilterArr->getForeignValue() > 0)
                        $convertedForeignValue = Currency::convertCurrency($fromCurrencyRightSym, $toCurrencyRightSym, $flighInfoFilterArr->getForeignValue());
                    else
                        $convertedForeignValue = $flighInfoFilterArr->getForeignValue();
                    $duty = 0;
                    if(empty($convertedForeignValue))
                        $convertedForeignValue = 0;
                    else    
                        $duty = ($convertedForeignValue*0.2);

                    $duty = $duty." ".$toCurrencyRightSym;
                    $convertedForeignValueWithCurrency = convertedForeignValue." ".$toCurrencyRightSym;
                    
                    $convertedValueForeignValueGB = $flighInfoFilterArr->getForeignValue();
                    if($flighInfoFilterArr->getCurrency() != 'GBP')
                        $convertedValueForeignValueGB = Currency::convertCurrency($flighInfoFilterArr->getCurrency(), "GBP", $flighInfoFilterArr->getForeignValue());

                    $countryDestName = "";
                    if($flighInfoFilterArr->getCountryId() > 0){
                        $countryDest = new Country($flighInfoFilterArr->getCountryId());
                        $countryDestName = $countryDest->getIso();
                    }
                    $parcelCount = 0;
                    if($flighInfoFilterArr->getCountryId() > 0){
                        $countryObj = new Country($flighInfoFilterArr->getCountryId());
                        $countryLowData = $countryObj->getBagLowValue();
                        if(empty($countryLowData))
                            $countryLowData = 0;
                        if($convertedForeignValue <= $countryLowData){
                            $conArr[] = $flighInfoFilterArr->getConsignmentId();
                            $excelData['mawb_list_lv'][] = $flighInfoFilterArr->getMawb();
                            $totalParcelLv += $flighInfoFilterArr->getPieces();
                            $parcelWightLv += $flighInfoFilterArr->getWeight();
                            $parcelValueLv += $flighInfoFilterArr->getForeignValue();
                            $excelData['flight_lv_data'][] = [
                                                                'mawb' => $flighInfoFilterArr->getMawb(),
                                                                'hawb' => $flighInfoFilterArr->getHawb(),
                                'awb' => $flighInfoFilterArr->getAwb(),
                                                                'from_country' => $countryOriganName,
                                                                'to_country' => $countryDestName,
                                                                'sender_name' => $flighInfoFilterArr->getSenderName(),
                                                                'receiver_name' => $flighInfoFilterArr->getContact(),
                                                                'weight' => $flighInfoFilterArr->getWeight(),
                                                                'description' => $flighInfoFilterArr->getDescription(),
                                                                'no_of_pieces' => $flighInfoFilterArr->getPieces(),
                                'foreign_value' => $convertedValueForeignValueGB,
                                                                'currency' => $flighInfoFilterArr->getCurrency(),
                                'custom_value' => $convertedForeignValue,
                                                                'duty' => $duty,
                                'tracking_no' => $flighInfoFilterArr->getTrackingNumber(),
                                'address_line_1' => $flighInfoFilterArr->getAddressLine1(),
                                'address_line_2' => $flighInfoFilterArr->getAddressLine2(),
                                'address_line_3' => $flighInfoFilterArr->getAddressLine3(),
                                'city' => $flighInfoFilterArr->getCity(),
                                'state' => $flighInfoFilterArr->getState(),
                                'postcode' => $flighInfoFilterArr->getPostcode(),
                                'sender_address_line_1' => $flighInfoFilterArr->getSenderAddressLine1(),
                                'sender_address_line_2' => $flighInfoFilterArr->getSenderAddressLine2(),
                                'sender_address_line_3' => $flighInfoFilterArr->getSenderAddressLine3(),
                                'sender_city' => $flighInfoFilterArr->getSenderCity(),
                                'sender_state' => $flighInfoFilterArr->getSenderState(),
                                'sender_postcode' => $flighInfoFilterArr->getSenderPostcode(),
                                'flight_number' => $flighInfoFilterArr->getFlightNumber()
                                                            ];
                        }else{
                            $conArr[] = $flighInfoFilterArr->getConsignmentId();
                            $totalParcelHv += $flighInfoFilterArr->getPieces();
                            $parcelWightHv += $flighInfoFilterArr->getWeight();
                            $parcelValueHv += $flighInfoFilterArr->getForeignValue();
                            $excelData['mawb_list_hv'][] = $flighInfoFilterArr->getMawb();
                            $excelData['flight_hv_data'][] = [
                                                                'mawb' => $flighInfoFilterArr->getMawb(),
                                                                'hawb' => $flighInfoFilterArr->getHawb(),
                                                                'from_country' => $countryOriganName,
                                                                'to_country' => $countryDestName,
                                                                'sender_name' => $flighInfoFilterArr->getSenderName(),
                                                                'receiver_name' => $flighInfoFilterArr->getContact(),
                                                                'weight' => $flighInfoFilterArr->getWeight(),
                                                                'description' => $flighInfoFilterArr->getDescription(),
                                                                'no_of_pieces' => $flighInfoFilterArr->getPieces(),
                                'foreign_value' => $convertedValueForeignValueGB,
                                                                'currency' => $flighInfoFilterArr->getCurrency(),
                                'custom_value' => $convertedForeignValue,
                                                                'duty' => $duty,
                                'tracking_no' => $flighInfoFilterArr->getTrackingNumber(),
                                'address_line_1' => $flighInfoFilterArr->getAddressLine1(),
                                'address_line_2' => $flighInfoFilterArr->getAddressLine2(),
                                'address_line_3' => $flighInfoFilterArr->getAddressLine3(),
                                'city' => $flighInfoFilterArr->getCity(),
                                'state' => $flighInfoFilterArr->getState(),
                                'postcode' => $flighInfoFilterArr->getPostcode(),
                                'sender_address_line_1' => $flighInfoFilterArr->getSenderAddressLine1(),
                                'sender_address_line_2' => $flighInfoFilterArr->getSenderAddressLine2(),
                                'sender_address_line_3' => $flighInfoFilterArr->getSenderAddressLine3(),
                                'sender_city' => $flighInfoFilterArr->getSenderCity(),
                                'sender_state' => $flighInfoFilterArr->getSenderState(),
                                'sender_postcode' => $flighInfoFilterArr->getSenderPostcode(),
                                'flight_number' => $flighInfoFilterArr->getFlightNumber()
                                                            ];
                        }
                    }
                }
                $flightObj = new FlightInfo($flightId);
                // Close the flight get all mawb of flight
                $flightFromCountry = '';
                if(!empty($flightObj->getCountryId())) {
                    $flightFromCountryObj = new Country($flightObj->getCountryId());
                    $flightFromCountry = $flightFromCountryObj->getName();
                }
                $flightToCountry = '';
                if(!empty($flightObj->getDestinationCountryId())) {
                    $flightToCountryObj = new Country($flightObj->getDestinationCountryId());
                    $flightToCountry = $flightToCountryObj->getName();
                }

                $excelData['mawb_list_lv'] = array_unique($excelData['mawb_list_lv']);
                $excelData['mawb_list_hv'] = array_unique($excelData['mawb_list_hv']);
                $excelData['flight_number'] = $flightNumber;//$flightObj->getFlightNumber();
                $excelData['flight_from_country'] = $flightFromCountry;
                $excelData['flight_to_country'] = $flightToCountry;
                $excelData['flight_total_parcel_lv'] = $totalParcelLv;
                $excelData['flight_total_parcel_weight_lv'] = $parcelWightLv;
                $excelData['flight_total_parcel_value_lv'] = $parcelValueLv;
                $excelData['flight_total_parcel_hv'] = $totalParcelHv;
                $excelData['flight_total_parcel_weight_hv'] = $parcelWightHv;
                $excelData['flight_total_parcel_value_hv'] = $parcelValueHv;
                if(count($excelData['flight_lv_data']) > 0){
                    $fileLv = getExcelFile($excelData,"lv");
                    $lowValueExcel = getExcelFileValueBased($excelData,'lv');
                }
                if(count($excelData['flight_hv_data']) > 0){
                    $fileHv = getExcelFile($excelData,"hv");
                    $highValueExcel = getExcelFileValueBased($excelData,"hv");
                }
                if(count($conArr) > 0){
                    $invoicesLink = generateInvoice($conArr);
                }
                if(!empty($fileLv))
                    $flightObj->setFilesLv($fileLv);
                if(!empty($fileHv))
                    $flightObj->setFilesHv($fileHv);
                if(!empty($lowValueExcel))
                    $flightObj->setLowValueManifest($lowValueExcel);
                if(!empty($highValueExcel))
                    $flightObj->setHighValueManifest($highValueExcel);
                if(!empty($invoicesLink))
                    $flightObj->setInvoice($invoicesLink);

                $flightObj->setIsClosed(1);
                $flightObj->setCarriageValue(0.0);
                $flightObj->setCustomValue(0.0);
                $flightObj->setInsuranceAmount(0.0);
                $flightObj->setIsDelete(0);
                $flightObj->save();
                $returnData['status'] = "success";
                $returnData['message'] = "Flight closed successfully";
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
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "get_flight_type") {
            $flightType = $this->form_vars['flight_type'];
            if($flightType=="flight"){
                $table = "flight AS fl";
                $type = "flight";
                $joinField = "fl.id";
                $fromJoinField = "f.transport_id";
                $column = 'f.*,fl.flight_number AS flight_number';
            }else if($flightType=="sea"){
                $table = "sea";
                $type = "sea";
                $joinField = "sea.id";
                $fromJoinField = "f.transport_id";
                $column = 'f.*,sea.ship_number AS flight_number';
            }else{
                $table = "truck";
                $type = "truck";
                $joinField = "truck.id";
                $fromJoinField = "f.transport_id";
                $column = 'f.*,truck.truck_number AS flight_number';
            }
            $flighInfoFilter = New FlighInfoFilter();
            $flighInfoFilter->addFieldFilter("       f.transport_type",$type);
            $flighInfoFilter->addJoin($table,$joinField,$fromJoinField,"JOIN");
            $flighInfoFilterObj = $flighInfoFilter->getColumnList($column);
            if(count($flighInfoFilterObj) > 0){
                $op = "<option value=''>Please Select Dispatch</option>";
                foreach ($flighInfoFilterObj as $flighInfoFilterArr) {
                    $op .= "<option value='".$flighInfoFilterArr->getId()."'>".$flighInfoFilterArr->getFlightNumber()."</option>";
                }
            }
            $output['status'] = "success";
            $output['op'] = $op;
            echo json_encode($output);
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "open_mawb_modal") {
            $html = "";
            $mawbId = $this->form_vars['mawb_id'];
            $mawbObj = New Mawb($mawbId);
            $mawbDestinationWarehouseId = $mawbObj->getMawbDestinationWarehouseId();
            if($mawbDestinationWarehouseId > 0 ){
                $wareHouse = New Warehouse($mawbDestinationWarehouseId);
                $wareHouse->getEmail();
                $html = $wareHouse->getEmail();
            }
            echo $html;
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "mawb_email_send") {
            $html = "";
            $subject = "";
            $mawbEmailId = trim($this->form_vars['mawb_email_id']);
            $mawbEmailCc = trim($this->form_vars['mawb_email_cc']);
            $flightInfonumber = trim($this->form_vars['flight_info_number']);
            $mawbEmailnumber = trim($this->form_vars['mawb_email_number']);
            $mawbEmailFlightId = trim($this->form_vars['mawb_email_flight_id']);
            $csvBagData = [];
            if($mawbEmailId > 0) {
                $mawbObj = New Mawb($mawbEmailId);
                $mawbDestinationWarehouseId = $mawbObj->getMawbDestinationWarehouseId();
                $mawbSourceWarehouseId = $mawbObj->getMawbSourceWarehouseId();
                $wareHouseSource = New Warehouse($mawbSourceWarehouseId);
                $wareHouse = New Warehouse($mawbDestinationWarehouseId);
                $flightFilter = New FlightInfo($mawbEmailFlightId);
                $timestampEtd = strtotime($flightFilter->getEtd());
                $timestampEta = strtotime($flightFilter->getEta());
                $etd =  date('d-m-Y h:i', $timestampEtd);
                $eta =  date('d-m-Y h:i', $timestampEta);
                $totalNumberOfBags = 0;
                $totalNumberOfparcels = 0;
                $totalWeight = 0;
                $mawbParcelMappingFilter = New MawbParcelMappingFilter();
                $mawbParcelMappingFilter->addFieldFilter("    mawb_id",$mawbEmailId);
                $mawbParcelMappingFilter->addFieldNotEqualFilter("    bag_id","0");
                $mawbParcelMappingFilter->addGroupBy("bag_id");
                $mawbParcelMappingObj = $mawbParcelMappingFilter->getColumnList("*");
                $totalNumberOfBags = count($mawbParcelMappingObj);
                $cbm = 0;
                if($totalNumberOfBags > 0){
                    foreach ($mawbParcelMappingObj as $mawbParcelMappingArr) {
                        $tempCbm = 0;
                        $bagNewObj = New Bagging($mawbParcelMappingArr->getBagId());
                        $bagWeight = 0;
                        $bagNewObj->getWeight();
                        $bagNewObj->getLength();
                        $bagNewObj->getWidth();
                        $bagNewObj->getHeight();
                        $tempCbm = (int) ($bagNewObj->getLength())*(int) ($bagNewObj->getWidth())*(int) ($bagNewObj->getHeight());
                        $tempCbm = $tempCbm/1000000;
                        $cbm += $tempCbm;
                        $bagNewObj->getActualWeight();
                        if($bagNewObj->getWeight() >  $bagNewObj->getActualWeight()){
                            $totalWeight += $bagNewObj->getWeight();
                        }else{
                            $totalWeight += $bagNewObj->getActualWeight();
                        }
                        $parcelBaggingMapping = New ParcelBaggingMappingFilter();
                        $parcelBaggingMapping->addFieldFilter("    bag_id",$mawbParcelMappingArr->getBagId());
                        $parcelBaggingMapping->addGroupBy("parcel_id");
                        $parcelBaggingMappingObj = $parcelBaggingMapping->getColumnList("*");
                        $totalNumberOfparcels = count($parcelBaggingMappingObj);
                        $csvBagData[$bagNewObj->getBagnumber()] = $totalNumberOfparcels;
                    }
                }

                $subject = "Dispatch pre alert Flight [ ".$flightInfonumber." ]  MAWB number [ ".$mawbEmailnumber." ]";
                $html = '<p class="x_x_x_MsoNormal" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; ">
       <span style="font-family :  &quot;Microsoft YaHei UI&quot;, &quot;sans-serif&quot;; color :  black; ">Dear All,&nbsp;</span>
    </p>
    <p class="x_x_x_MsoNormal" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; ">
       <span style="font-family :  &quot;Microsoft YaHei UI&quot;, &quot;sans-serif&quot;; color :  black; ">Please refer to volume forecast as below table.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       </span>
    </p>';
                $html .= '<table class="x_x_x_MsoNormalTable" border="0" cellspacing="0" cellpadding="0" width="1571" style="width :  1178.5pt; border-collapse :  collapse; ">
       <tbody>
          <tr style="height :  33.4pt; ">
             <td width="auto" style="width :  124.3pt; border :  solid black 1.0pt; background :  #8DB4E2; padding :  0cm 5.4pt 0cm 5.4pt; height :  33.4pt; ">
                <p class="x_x_x_MsoNormal" align="center" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; text-align :  center; ">
                   <b><span style="font-size :  10.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  black; ">MAWB Number</span></b>
                </p>
             </td>
             <td width="auto" style="width :  56.4pt; border :  solid black 1.0pt; border-left :  none; background :  #8DB4E2; padding :  0cm 5.4pt 0cm 5.4pt; height :  33.4pt; ">
                <p class="x_x_x_MsoNormal" align="center" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; text-align :  center; ">
                   <b><span style="font-size :  10.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  black; ">Origin port</span></b>
                </p>
             </td>
             <td width="auto" style="width :  78.6pt; border :  solid black 1.0pt; border-left :  none; background :  #8DB4E2; padding :  0cm 5.4pt 0cm 5.4pt; height :  33.4pt; ">
                <p class="x_x_x_MsoNormal" align="center" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; text-align :  center; ">
                   <b><span style="font-size :  10.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  black; ">Destination port</span></b>
                </p>
             </td>
             <td width="auto" style="width :  273.75pt; border :  solid black 1.0pt; border-left :  none; background :  #8DB4E2; padding :  0cm 5.4pt 0cm 5.4pt; height :  33.4pt; ">
                <p class="x_x_x_MsoNormal" align="center" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; text-align :  center; ">
                   <b><span style="font-size :  10.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  black; ">Flight Number</span></b>
                </p>
             </td>
             <td width="auto" style="width :  53.2pt; border :  solid black 1.0pt; border-left :  none; background :  #8DB4E2; padding :  0cm 5.4pt 0cm 5.4pt; height :  33.4pt; ">
                <p class="x_x_x_MsoNormal" align="center" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; text-align :  center; ">
                   <b><span style="font-size :  10.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  black; ">ETD</span></b>
                </p>
             </td>
             <td width="auto" style="width :  55.45pt; border :  solid black 1.0pt; border-left :  none; background :  #8DB4E2; padding :  0cm 5.4pt 0cm 5.4pt; height :  33.4pt; ">
                <p class="x_x_x_MsoNormal" align="center" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; text-align :  center; ">
                   <b><span style="font-size :  10.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  black; ">ETA</span></b>
                </p>
             </td>
             <td width="auto" style="width :  65.7pt; border :  solid black 1.0pt; border-left :  none; background :  #8DB4E2; padding :  0cm 5.4pt 0cm 5.4pt; height :  33.4pt; ">
                <p class="x_x_x_MsoNormal" align="center" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; text-align :  center; ">
                   <b><span style="font-size :  10.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  black; ">Number of Bag</span></b>
                </p>
             </td>
             <td width="auto" style="width :  87.35pt; border :  solid black 1.0pt; border-left :  none; background :  #8DB4E2; padding :  0cm 5.4pt 0cm 5.4pt; height :  33.4pt; ">
                <p class="x_x_x_MsoNormal" align="center" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; text-align :  center; ">
                   <b><span style="font-size :  10.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  black; ">GW(kg)</span></b>
                </p>
             </td>
             <td width="auto" style="width :  40.7pt; border :  solid black 1.0pt; border-left :  none; background :  #8DB4E2; padding :  0cm 5.4pt 0cm 5.4pt; height :  33.4pt; ">
                <p class="x_x_x_MsoNormal" align="center" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; text-align :  center; ">
                   <b><span style="font-size :  10.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  black; ">CBM</span></b>
                </p>
             </td>
             <td width="auto" style="width :  81.95pt; border :  solid black 1.0pt; border-left :  none; background :  #8DB4E2; padding :  0cm 5.4pt 0cm 5.4pt; height :  33.4pt; ">
                <p class="x_x_x_MsoNormal" align="center" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; text-align :  center; ">
                   <b><span style="font-size :  10.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  black; ">Number of packages</span></b>
                </p>
             </td>
             <td width="auto" style="width :  175.55pt; border :  solid black 1.0pt; border-left :  none; background :  #8DB4E2; padding :  0cm 5.4pt 0cm 5.4pt; height :  33.4pt; ">
                <p class="x_x_x_MsoNormal" align="center" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; text-align :  center; ">
                   <b><span style="font-size :  10.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  black; ">Estimated handover to APEX SZX time</span></b>
                </p>
             </td>
          </tr>';
// End header
                $detinationPort = explode('-',$wareHouse->getWarehouseCode());
          $html .= '<tr style="height :  24.95pt; ">
             <td width="auto" rowspan="3" style="width :  124.3pt; border :  solid black 1.0pt; border-top :  none; padding :  0cm 5.4pt 0cm 5.4pt; height :  24.95pt; ">
                <p class="x_x_x_MsoNormal" align="center" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; text-align :  center; ">
                   <span style="font-size :  14.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  #1F497D; ">'.$mawbEmailnumber.'</span><span style="font-size :  14.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  #1F497D; "></span>
                </p>
             </td>
             <td width="auto" rowspan="3" style="width :  56.4pt; border-top :  none; border-left :  none; border-bottom :  solid black 1.0pt; border-right :  solid black 1.0pt; padding :  0cm 5.4pt 0cm 5.4pt; height :  24.95pt; ">
                <p class="x_x_x_MsoNormal" align="center" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; text-align :  center; ">
                   <span style="font-size :  14.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  black; ">LHR</span>
                </p>
             </td>
             <td width="auto" rowspan="3" style="width :  78.6pt; border-top :  none; border-left :  none; border-bottom :  solid black 1.0pt; border-right :  solid black 1.0pt; padding :  0cm 5.4pt 0cm 5.4pt; height :  24.95pt; ">
                <p class="x_x_x_MsoNormal" align="center" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; text-align :  center; ">
                   <span style="font-size :  14.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  black; ">'.$detinationPort[0].'</span>
                </p>
             </td>
             <td width="auto" rowspan="3" style="width :  273.75pt; border-top :  none; border-left :  none; border-bottom :  solid black 1.0pt; border-right :  solid black 1.0pt; padding :  0cm 5.4pt 0cm 5.4pt; height :  24.95pt; ">
                <p class="x_x_x_MsoNormal" align="center" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; text-align :  center; ">
                   <span style="font-size :  14.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  #1F497D; ">'.$flightInfonumber.'</span><span style="font-size :  14.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  #C00000; "></span>
                </p>
             </td>
             <td width="auto" rowspan="3" style="width :  53.2pt; border-top :  none; border-left :  none; border-bottom :  solid black 1.0pt; border-right :  solid black 1.0pt; padding :  0cm 5.4pt 0cm 5.4pt; height :  24.95pt; ">
                <p class="x_x_x_MsoNormal" align="center" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; text-align :  center; ">
                   <span style="font-size :  14.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  #1F497D; ">'.$etd.'</span>
                </p>
             </td>
             <td width="auto" rowspan="3" style="width :  55.45pt; border-top :  none; border-left :  none; border-bottom :  solid black 1.0pt; border-right :  solid black 1.0pt; padding :  0cm 5.4pt 0cm 5.4pt; height :  24.95pt; ">
                <p class="x_x_x_MsoNormal" align="center" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; text-align :  center; ">
                   <span style="font-size :  14.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  #1F497D; ">'.$eta.'</span>
                </p>
             </td>';
            // total data start
             $html .= '
                        <td width="auto" rowspan="3" style="width :  175.55pt; border-top :  none; border-left :  none; border-bottom :  solid black 1.0pt; border-right :  solid black 1.0pt; background :  yellow; padding :  0cm 5.4pt 0cm 5.4pt; height :  24.95pt; ">
                            <p class="x_x_x_MsoNormal" align="center" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; text-align :  center; ">
                               <span style="font-size :  14.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  #1F497D; ">'.$totalNumberOfBags.'</span>
                            </p>
                         </td>
                         <td width="auto" rowspan="3" style="width :  175.55pt; border-top :  none; border-left :  none; border-bottom :  solid black 1.0pt; border-right :  solid black 1.0pt; background :  yellow; padding :  0cm 5.4pt 0cm 5.4pt; height :  24.95pt; ">
                            <p class="x_x_x_MsoNormal" align="center" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; text-align :  center; ">
                               <span style="font-size :  14.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  #1F497D; ">'.$totalWeight.'</span>
                            </p>
                         </td>
                         <td width="auto" rowspan="3" style="width :  175.55pt; border-top :  none; border-left :  none; border-bottom :  solid black 1.0pt; border-right :  solid black 1.0pt; background :  yellow; padding :  0cm 5.4pt 0cm 5.4pt; height :  24.95pt; ">
                            <p class="x_x_x_MsoNormal" align="center" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; text-align :  center; ">
                               <span style="font-size :  14.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  #1F497D; ">'.$cbm.'</span>
                            </p>
                         </td>
                         <td width="auto" rowspan="3" style="width :  175.55pt; border-top :  none; border-left :  none; border-bottom :  solid black 1.0pt; border-right :  solid black 1.0pt; background :  yellow; padding :  0cm 5.4pt 0cm 5.4pt; height :  24.95pt; ">
                            <p class="x_x_x_MsoNormal" align="center" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; text-align :  center; ">
                               <span style="font-size :  14.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  #1F497D; ">'.$totalNumberOfparcels.'</span>
                            </p>
                         </td>
                        <td width="auto" rowspan="3" style="width :  175.55pt; border-top :  none; border-left :  none; border-bottom :  solid black 1.0pt; border-right :  solid black 1.0pt; background :  yellow; padding :  0cm 5.4pt 0cm 5.4pt; height :  24.95pt; ">
                            <p class="x_x_x_MsoNormal" align="center" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; text-align :  center; ">
                               <span style="font-size :  14.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  #1F497D; "></span><span style="font-size :  14.0pt; font-family :  &quot;Arial&quot;, &quot;sans-serif&quot;; color :  #1F497D; ">'.$etd.'</span>
                            </p>
                         </td>';

          $html .= '</tr>
       </tbody>
    </table>';
            }
            
            /*
             * Footer
             */
            $html .= '<p class="x_x_x_MsoNormal" style="margin :  0cm 0cm 0.0001pt; text-align :  justify; font-size :  10.5pt; font-family :  Calibri, sans-serif; ">
       <span style="font-family :  &quot;Microsoft YaHei UI&quot;, &quot;sans-serif&quot;; color :  black; "><br /><br />Kind Regards,&nbsp;</span>
    </p>';
            $return['status'] = "error";
            $return['message'] = "Mail sent successfully";
            $mawbEmail = trim($this->form_vars['mawb_email']);
            if(!empty($mawbEmail) && !empty($html)){
                // create CSv to attache
                $csv = "";
                $cr = "\r\n";
                $count = 1;
                $csvNote = "";
                $name = '';
                $csvNote .= "MAWB pre-alert:" . $cr;
                $csvNote .= "Date:" . Date("d-m-Y") . $cr;
                $header_row = array(
                    "MAWB Number",
                    "Bag Number",
                    "Shipment Count"
                );
                $record = "";
                foreach ($header_row as $field) {
                    $record .= $field . ",";
                }
                $record .= "\r\n";
                $csvHeader = $record;
                if(count($csvBagData) > 0){
                    foreach ($csvBagData as $bagnumber => $parcelCount) {
                        $csv .= $mawbEmailnumber . ",";
                        $csv .= $bagnumber . ",";
                        $csv .= $parcelCount;
                        $csv .= $cr;
                    }
                }
                $folder = '../_assets/export_manifest';

                if (!file_exists($folder)) {
                    mkdir($folder, 0777, true);
                }
                $fileName = $mawbEmailnumber.'_'.date("Y-m-d").'_'.time().'.csv';
                $fullPathName = $folder."/".$fileName;
                $file_path_new = fopen($fullPathName, 'w');
                fwrite($file_path_new, $csvNote . $csvHeader . $cr . $csv);
                fclose($file_path_new);
                $mailMawb = mail_attachment($fileName,SETTING_DIR_ASSETS."export_manifest/",$mawbEmail,"ops@oneworldexpress.com","ops","itsupport@oneworldexpress.com",$subject,$html,$mawbEmailCc);

                if ($mailMawb == "success"){
                    $return['status'] = "success";
                    $return['message'] = "Mail sent successfully";
                }
            }
            echo json_encode($return);
            die;
        }
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "oc_bag") {
            $return['status'] = "error";
            $return['message'] = "Label cannot be generated";
            $bagId = $this->form_vars['bag_id'];
            $mawbNumber = $this->form_vars['mawb_number'];
            $flightInfoNumber = $this->form_vars['flight_info_number'];
            $ocBag = New OcBagLabel();
            $ocBagRtn = $ocBag->generateOcBagLabel($bagId,$mawbNumber,$flightInfoNumber);
            if(isset($ocBagRtn['STATUS']) && $ocBagRtn['STATUS'] == "SUCCESS"){
                $ocBagLabel = "_assets/".$ocBagRtn['LABEL'];
                $bagging = New Bagging($bagId);
                $bagging->setOcBagLabel($ocBagLabel);
                $bagging->save();
                $return['status'] = "success";
                $return['message'] = "Label generated successfully";
                $return['label'] = $ocBagLabel;
            }
            echo json_encode($return);
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
                parcelDataTableFun.init();
                var flight_id = $("#flight").val();
                get_flight_mawb_stats(flight_id);
                $(".reload").click(function () {
                    var flight_id = $("#flight").val();
                    get_flight_mawb_stats(flight_id);
                });
                
                // Add multiple Items of Parcel
                $(".initialitem-button").hide();
                var elindex_item = 0;
                $(document).on('click', '.add-more-item-keys', function () {
                    elindex_item++;
                    var clone = $(this).parent().parent().parent().clone();
                    var mawbqty = $(clone).find('.mawb_qty').attr('name');
                    var itemlength = $(clone).find('.item_length').attr('name');
                    var itemwidth = $(clone).find('.item_width').attr('name');
                    var itemheight = $(clone).find('.item_height').attr('name');
                    $(this).remove();
                    $(clone).find('.mawb_qty').val('');
                    $(clone).find('.mawb_qty').attr('name', mawbqty.replace(/\d+/, elindex_item));
                    $(clone).find('.item_length').attr('name', itemlength.replace(/\d+/, elindex_item));
                    $(clone).find('.item_width').attr('name', itemwidth.replace(/\d+/, elindex_item));
                    $(clone).find('.item_height').attr('name', itemheight.replace(/\d+/, elindex_item));
                    $(clone).find('button.remove-item-key').show();
                    $(clone).find('button.remove-item-key').removeClass('initialitem-button');
                    $(clone).appendTo($('.ItemInformation'));
                });

                //Remove Multiple Items of Parcel
                $(document).on('click', '.remove-item-key', function () {
                    var el = $(this);
                    var removeItemsFlag = $(el).data('totalremoveitems');
                    if(removeItemsFlag != 'yes'){
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
                                    if ($('.ItemInformation .remove-item-key').length > 1) {
                                        $(el).parent().parent().remove();
                                        if ($('.add-more-item-keys').length == 0) {
                                            var addMore = $(el).parent().find('.add-more-item-keys').clone();
                                            $('.ItemInformation .remove-item-key').last().parent().prepend(addMore);
                                        }
                                        if ($(".item_description").length == 1) {
                                            $(".ItemInformation .remove-item-key").hide();
                                        }
                                    } else {
                                        $(el).parent().parent().find('input').val('');
                                    }
                                }
                            });
                    }
                    else
                    {
                        if ($('.ItemInformation .remove-item-key').length > 1) {
                            $(el).parent().parent().remove();
                            if ($('.add-more-item-keys').length == 0) {
                                var addMore = $(el).parent().find('.add-more-item-keys').clone();
                                $('.ItemInformation .remove-item-key').last().parent().prepend(addMore);
                            }
                            if ($(".item_description").length == 1) {
                                $(".ItemInformation .remove-item-key").hide();
                            }
                        } else {
                            $(el).parent().parent().find('input').val('');
                        }
                    }
                });
                
                $('body').on('change', '#mawb_class', function () {
                    var that = $(this);
                    if(that.val() != '') {
                        $('#mawb_number').attr('readonly', 'readonly');
                    } else {
                        $('#mawb_number').removeAttr('readonly');
                    }
                });
                $("#create_mawb").click(function () {
                    swal({
                        title: "Are you sure you want to create new MAWB",
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
                                    var frmData = $("#itemdetail_save").serializeArray();
                                    var flight_id = $("#flight").val();
                                    frmData.push({name: "flight_id", value: flight_id});
                                    var mawb_number = $("#mawb_number").val();
                                    var mawb_class = $("#mawb_class").val();
                                    var destination_country_id = $("#destination_country_id").val();
                                    var destination_warehouse_id = $("#destination_warehouse_id").val();
                                    var gross_weight = $("#gross_weight").val();
                                    var chargeable_weight = $("#chargeable_weight").val();
                                    
                                    if (flight_id > 0) {
                                        $.ajax({
                                            url: "box_ajax.php", // point to server-side PHP script
                                            data: frmData,
                                            dataType: "json",
                                            type: 'post',
                                            success: function (php_script_response) {
                                                $("#mawb_number").val("");
                                                $("#create_mawb_modal").modal("hide");
                                                if (php_script_response.response == "success") {
                                                    var volWeight = php_script_response.mawb_volWeight;
                                                    $('#chargeable_weight').val(volWeight);
                                                    $("#mawb").append('<option value="' + php_script_response.mawb_number + '">' + php_script_response.mawb + '</option>');
                                                    $("#mawb option:contains(" + php_script_response.mawb + ")").attr('selected', 'selected');
                                                    $("#mawb").select2();
                                                        $("#show_general_msg div.alert").html(" ");
                                                        $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                                                        $("#show_general_msg div.alert").html(php_script_response.msg);
                                                        $("#show_general_msg").show();
                                                } else {
                                                    $("#show_general_msg div.alert").html(" ");
                                                    $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                                                    $("#show_general_msg div.alert").html(php_script_response.msg);
                                                    $("#show_general_msg").show();
                                                }
                                            }
                                        });
                                    } else {
                                        swal("Alert", "Please select flight number", "info");
                                    }
                                }
                            });
                });
                $('#flight_rdo').click();
            });
            $(document).on('click', '.flight_type', function () {
                var flightType = $(this).val();
                $.ajax({
                    url: "mawb_flight_manage.php", // point to server-side PHP script
                    data: {
                        flight_type: flightType,
                        action: 'get_flight_type'
                    },
                    dataType: "json",
                    type: 'post',
                    success: function (php_script_response) {
                        if (php_script_response.status == "success") {
                            $("#flight").html(" ");
                            $("#flight").html(php_script_response.op);
                        }
                    }
                });
            });
            function get_mawb(flight_id) {
                if (flight_id > 0) {
                    get_flight_mawb_stats(flight_id);
                    $.ajax({
                        url: "mawb_flight_manage.php", // point to server-side PHP script
                        dataType: 'html', // what to expect back from the PHP script, if anything
                        data: {action: 'get_mawb', flight_id: flight_id},
                        type: 'post',
                        success: function (php_script_response) {
                            if (php_script_response == "") {
                            } else {
                                $("#mawb").html("");
                                $("#mawb").append(php_script_response);
                            }
                        }
                    });
                }
            }
            function checkKeyValue(e) {
                if (e.keyCode == 13) {
                    var tracking_number = $("#tracking_number").val();
                    var flight_id = $("#flight").val();
                    var mawb_id = $("#mawb").val();
                    var message = "";
                    var message_class = "success";
                    $("#show_general_msg div.alert").html(" ");
                    $("#show_general_msg div.alert").removeClass('alert-success');
                    $("#show_general_msg div.alert").removeClass('alert-danger');
                    if (tracking_number == "") {
                        message = "Enter tracking number";
                        message_class = "danger";
                    }
                    if (mawb_id == "") {
                        message = "Select MAWB";
                        message_class = "danger";
                    }
                    if (flight_id == "") {
                        message = "Select flight";
                        message_class = "danger";
                    }
                    if (message) {
                        $("#show_general_msg div.alert").addClass('alert-' + message_class);
                        $("#show_general_msg div.alert").html(message);
                        $("#show_general_msg").show();
                    } else {
                        $.ajax({
                            url: "mawb_flight_manage.php", // point to server-side PHP script
                            dataType: 'html', // what to expect back from the PHP script, if anything
                            data: {action: 'add_tracking', flight_id: flight_id, mawb_id: mawb_id, tracking_number: tracking_number},
                            type: 'post',
                            success: function (php_script_response) {
                                $("#tracking_number").val('');
                                php_script_response = $.parseJSON(php_script_response);
                                get_flight_mawb_stats(flight_id);
                                $("#tracking_number").val("");
                                if (php_script_response.status == "success") {
                                    $("#show_general_msg div.alert").html(" ");
                                    $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                                    $("#show_general_msg div.alert").html(php_script_response.message);
                                    $("#show_general_msg").show();
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
            }
            $(document).on('click', '.close_bag', function () {
                var bag_id = $(this).data('bag_id');
                var mawb_id = $(this).data('mawb_id');
                var flight_id = $("#flight").val();
                var flightType = $('input[name="flight_type"]:checked').val();
                if(flightType == "flight"){
                    swal({
                            title: "Are you sure you want to close the bag",
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
                                    url: "mawb_flight_manage.php", // point to server-side PHP script
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
                                            get_flight_mawb_stats(flight_id);
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
                }else{
                    $("#bag_detail_bag_id").val(bag_id);
                    $("#bag_detail_mawb_id").val(mawb_id);
                    $("#bag_details").modal("show");
                }
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
                    url: "mawb_flight_manage.php", // point to server-side PHP script
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
                                url: "mawb_flight_manage.php", // point to server-side PHP script
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
                                        get_flight_mawb_stats(flight_id);
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
                                    url: "mawb_flight_manage.php", // point to server-side PHP script
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
                                            get_flight_mawb_stats(flight_id);
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
            $(document).on('click', '.close_mawb', function () {
                var mawb_id = $(this).data("mawb_id");
                var flight_id = $("#flight").val();
                var add_track_point = $("#add_track_point").val();
                swal({
                    title: "Are you sure you want to dispatch the MAWB?",
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
                                    data: {action: "close_mawb", mawb_id: mawb_id, dispatch: 'd',flight_id:flight_id,add_track_point:add_track_point},
                                    dataType: "json",
                                    success: function (data) {
                                        get_flight_mawb_stats(flight_id);
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
            $(document).on('click', '.mawb_partial_dispatch', function () {
                var mawb_id = $(this).data("mawb_id");
                var flight_id = $("#flight").val();
                var add_track_point = $("#add_track_point").val();
                swal({
                    title: "Are you sure you want to partial dispatch the MAWB",
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
                                    data: {action: "close_mawb", mawb_id: mawb_id, dispatch: 'pd',add_track_point:add_track_point},
                                    dataType: "json",
                                    success: function (data) {
                                        get_flight_mawb_stats(flight_id);
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
            $(document).on('click', '.close_flight_btn', function () {
                var flight_id = $("#flight").val();
                swal({
                    title: "Are you sure you want to close flight",
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
                                    data: {action: "close_flight", flight_id: flight_id},
                                    dataType: "json",
                                    success: function (data) {
                                        get_flight_mawb_stats(flight_id);
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
                                        get_flight_mawb_stats(flight_id);
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
            function get_flight_mawb_stats(flight_id) {
                // var flight_id = $("#flight").val();
                // var flight_id = flight_id;
                // alert(flight_id);
                if (flight_id != '') {
                    $.ajax({
                        url: "mawb_flight_manage.php", // point to server-side PHP script
                        data: {
                            flight_id: flight_id,
                            action: 'get_flight_mawb_stats'
                        },
                        dataType: "json",
                        type: 'post',
                        success: function (php_script_response) {
                            if (php_script_response.status == "success") {
                                $("#flight_info_stats").html(php_script_response.html);
                                $("#flight_info_number").html(php_script_response.flight_number);
                                $('#add_track_point').bootstrapSwitch('state', true);
                            } else {

                            }
                            $('[data-toggle="tooltip"]').tooltip();
        //                            check_mawb_bag();
                        }
                    });
                } else {
                    $("#flight_info_stats").html('No flight is selected.');
                }
            }
            var parcelGrid = null;
            var parcelDataTableFun = function () {
                var BaghandleDataTable = function () {
                    var bag_id = $('#bag_filter').val();
                    var bagdatatableurl = "mawb_flight_manage.php?action=parcel_detail_ajax";
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
                                url: 'mawb_flight_manage.php',
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
            $(document).on('click', '#create_mawb_btn', function (){
                var destination_country_flight =  $("#p_destination_country_id").attr("data-country_id");
                if (typeof destination_country_flight != "undefined") {
                    $("#destination_country_id").val(destination_country_flight);
                    $('#destination_country_id').selectpicker("refresh");
                    $('#destination_country_id').change();
                }
                $("#create_mawb_modal").modal("show");
            });
            $(document).on('click', '.pre_alert_mawb', function () {
                var mawb_id = $(this).attr("data-mawb_id");
                var flight_id = $(this).attr("data-flight_id");
                var mawb_number = $(this).attr("id");
                $.ajax({
                    url: "mawb_flight_manage.php", // point to server-side PHP script
                    data: {
                        mawb_id: mawb_id,
                        action: 'open_mawb_modal'
                    },
                    dataType: "html",
                    type: 'post',
                    success: function (php_script_response) {
                        if (php_script_response != "") {
                            $("#mawb_email").val(php_script_response);
                            $("#mawb_email_id").val(mawb_id);
                            $("#mawb_email_number").val(mawb_number);
                            $("#mawb_email_flight_id").val(flight_id);
                            $("#mawb_email_modal").modal("show");
                        }
                    }
                });
            });
            $(document).on('click', '#send_mawb_email', function () {
                var mawb_email = $("#mawb_email").val();
                var mawb_email_cc = $("#mawb_email_cc").val();
                var mawb_email_id = $("#mawb_email_id").val();
                var mawb_email_flight_id = $("#mawb_email_flight_id").val();
                var mawb_email_number = $("#mawb_email_number").val();
                var flight_info_number = $("#flight_info_number").html();
                $.ajax({
                    type: "POST",
                    url: "mawb_flight_manage.php",
                    data: {action: "mawb_email_send", mawb_email: mawb_email,mawb_email_id:mawb_email_id,flight_info_number:flight_info_number,mawb_email_number:mawb_email_number,mawb_email_flight_id:mawb_email_flight_id,mawb_email_cc:mawb_email_cc},
                    dataType: "json",
                    success: function (data) {
                        if(data.status == "success") {
                            $("#show_general_msg div.alert").html(" ");
                            $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                            $("#show_general_msg div.alert").html(data.message);
                            $("#show_general_msg").show();
                            $("#mawb_email_modal").modal("hide");
                        } else {
                            $("#show_general_msg div.alert").html(" ");
                            $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                            $("#show_general_msg div.alert").html(data.message);
                            $("#show_general_msg").show();
                            $("#mawb_email_modal").modal("hide");
                        }
                    },
                    error: function () {
                        alert('error occur');
                    }
                });
            });
            $(document).on('click', '.oc_bag_label', function () {
                var bag_id = $(this).data("bag_id");
                var mawb_number = $(this).data("mawb_number");
                var flight_info_number = $("#flight_info_number").html();
                $.ajax({
                    url: "mawb_flight_manage.php", // point to server-side PHP script
                    data: {
                        bag_id: bag_id,
                        flight_info_number: flight_info_number,
                        mawb_number: mawb_number,
                        action: 'oc_bag'
                    },
                    dataType: "json",
                    type: 'post',
                    success: function (php_script_response) {
                        if (php_script_response.status == "success") {
                            $("#show_general_msg div.alert").html(" ");
                            $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                            $("#show_general_msg div.alert").html(php_script_response.message);
                            $("#show_general_msg").show();
                            newwindow = window.open(php_script_response.label, 'Label', 'height=400,width=400');
                            if (window.focus) {
                                newwindow.focus()
                            }
                            $(".reload").click();
                        } else {
                            $("#show_general_msg div.alert").html(" ");
                            $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                            $("#show_general_msg div.alert").html(php_script_response.message);
                            $("#show_general_msg").show();
                        }
                    }
                });
            });
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
                           Add Dispatch Info
                        </div>                        
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="alert" id="general-response-message" style="display: none;"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mt-radio-inline">
                                    <label class="mt-radio">
                                    <label>&nbsp;</label>
                                        <input id="flight_rdo" type="radio" name="flight_type" class="flight_type" value="flight"> International
                                        <span></span>
                                    </label>
                                    <label class="mt-radio">
                                        <label>&nbsp;</label>
                                        <input type="radio" name="flight_type" class="flight_type" value="truck" checked="checked"> Domestic
                                        <span></span>
                                    </label>
                                    <label class="mt-radio">
                                        <label>&nbsp;</label>
                                        <input type="radio" name="flight_type" class="flight_type" value="sea"> Sea
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="label-control">Dispatch</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
                                        <?php echo Ddl::generateDDL('flight', 'FlighInfoFilter', ' is_closed = 0 AND is_delete = 0 ', 'flight_number', 'id', $flight, ' class="form-control select2" rel="tooltip" onchange="get_mawb(this.value)"', "Please Select Dispatch", '', 'flight', ''); ?>
                                        <span class="input-group-btn"><a href="add_flight.php" target="_blank" class="btn btn-success"><i class="fa fa-plus"></i></a></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="label-control">MAWB</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
                                        <select name="mawb" id="mawb" class="form-control select2" rel="tooltip" title="" data-container="body" placeholder="" tabindex="-1" aria-hidden="true" data-original-title="">
                                            <option value="">Please select Mawb</option>
                                        </select>
                                        <span class="input-group-btn"><button type="button" class="btn btn-success" id="create_mawb_btn"><i class="fa fa-plus"></i></button></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="label-control">Tracking No / Bag No.</label>
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
                            Dispatch Info [ <span id="flight_info_number"></span> ]
                        </div>
                        <div class="tools">
                            <a href="javascript:;" class="reload" data-original-title="" title=""> </a>
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
            <form name="itemdetail_save" id="itemdetail_save" action="" method="post">
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
                        <div class="col-sm-6">
                            <div class="form-group">

                                <div class="has-float-label input-icon right">
                                    <div class="first_form_col">
                                        <?php
                                        echo Ddl::generateCountryDDL('destination_country_id', "", 'id', ' class="form-filter bs-select form-control" required="" data-live-search="true" data-size="8" onChange=get_destination_warehouse();');
                                        ?> <label>Destination Country  <span class="red-18">*</span></label>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">

                                <div class="has-float-label input-icon right">
                                    <div class="first_form_col">

                                        <!-- User Document -->
                                        <select name="destination_warehouse_id" id="destination_warehouse_id" class="bs-select form-control" data-live-search="true" data-show-subtext="true" data-toggle="tooltip" title="" data-original-title="Destination Warehouse" data-container="body" placeholder="Destination Warehouse">
                                        </select><label>Destination Warehouse <span class="red-18">*</span></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                  <div class="row">
                      <div class="col-md-6">
                            <div class="form-group">
                                <label class="label-control">Gross Weight</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
                                    <input type="text" name="gross_weight" id="gross_weight" class="form-control" placeholder="Gross Weight"/>
                                </div>
                            </div>    
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="label-control">Chargeable Weight</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
                                    <input type="text" name="chargeable_weight" id="chargeable_weight" class="form-control" placeholder="Chargeable Weight"/>
                                </div>
                            </div>    
                        </div>
                  </div>
                  <div class="row">        
                    <div class="col-md-2">
                        <label>Quantity</label>
                    </div>
                    <div class="col-md-2">
                        <label>Length</label>
                    </div>
                    <div class="col-md-2">
                        <label>Width<!--<span class="required" aria-required="true"> * </span>--></label>
                    </div>
                    <div class="col-md-2">
                        <label>Height<!--<span class="required" aria-required="true"> * </span>--></label>
                    </div>
                      <div class="col-md-4"></div>
                </div>
                <div class="ItemInformation">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-2">
                                <input type='text'  name='item[0][mawb_qty]' value='<?php echo $mawb_qty; ?>'  placeholder="Quantity"  class="form-control mawb_qty"  rel="tooltip" title="Quantity" />
                            </div>
                            <div class="col-md-2">
                                <input type='text'  name='item[0][item_length]'   value='<?php echo $item_length; ?>'   placeholder="Length"  class="form-control item_length"  rel="tooltip" title="Length"  />
                            </div>
                            <div class="col-md-2">
                                <input type='text'  name='item[0][item_width]' value='<?php echo $item_width; ?>'  placeholder="Width"  class="form-control item_width"  rel="tooltip" title="Width"   />
                            </div>
                            <div class="col-md-2">
                                <input type='text'  name='item[0][item_height]' value='<?php echo $item_height; ?>'  placeholder="Height"  class="form-control item_height"  rel="tooltip" title="Height"   />
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-success add-more-item-keys"><i class="fa fa-plus"></i></button>
                                <button type="button" class="btn btn-danger remove-item-key initialitem-button"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="create_mawb">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <input type="hidden" name="action" value="create_mawb" />
              </div>
            </div>

          </div>
            </form>
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
                                                            <input type="text" name="tracking_number" id="tracking_number" class="form-control form-filter" >
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
        <div id="mawb_email_modal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Mawb Pre alert Email</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="label-control">MAWB Email</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
                                        <input type="text" name="mawb_email" id="mawb_email" class="form-control" placeholder="mawb email"/>
                                        <input type="hidden" name="mawb_email_id" id="mawb_email_id" />
                                        <input type="hidden" name="mawb_email_number" id="mawb_email_number" />
                                        <input type="hidden" name="mawb_email_flight_id" id="mawb_email_flight_id" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="label-control">MAWB Email CC</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-addon"> <i class="fa fa-globe"></i></span>
                                        <input type="text" name="mawb_email_cc" id="mawb_email_cc" class="form-control" placeholder="mawb email cc"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="send_mawb_email">Send</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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

function createBagWithServiceId($serviceId, $sourceCountryId, $destinationCountryId,$bagLowValue) {
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
    $bagging->setBagSourceWarehouseId("");
    $bagging->setBagDestinationCountryId($destinationCountryId);
    $bagging->setBagDestinationWarehouseId("");
    $bagging->setBagType("NORMAL");
    $bagging->setService($serviceId);
    $bagging->setBagValue($bagLowValue);
    $bagging->setBagStatus(1);
    $bagging->setIsClosed(0);
    $bagging->setDateUpdated(time());
    $bagging->setIsCountryBagging("n");
    $bagging->save();
    if ($bagging->getId() > 0) {
        $returnData['bag_id'] = $bagging->getId();
        $returnData['bag_number'] = $bagnumber;
    }
    return json_encode($returnData);
    die;
}
function addDataIntoMappingTable($bagId, $mawbId, $parcelId, $flightId,$bagNumber="",$isLowValBag="lv",$trackingNumber) {
    $user = SessionManager::getUser();
    $returnData['status'] = "success";
    $parcelvalue = '<span class="label label-sm  label-success">LV</span>';
    if($isLowValBag == "hv")
        $parcelvalue = '<span class="label label-sm  label-info">HV</span>';
    
    $returnData['message'] = '<span style="font-size: 30px;color: black;">'.$isLowValBag.'</span><br />Bag Number ['.$bagNumber.'] Parcel Number ['.$trackingNumber.']';
//    check if data is already into table
    $mawbParcelMappingFilter = new MawbParcelMappingFilter();
    $mawbParcelMappingFilter->addFieldFilter("    mawb_id", $mawbId);
    $mawbParcelMappingFilter->addFieldFilter("parcel_id", $parcelId);
    $mawbParcelMappingFilter->addFieldFilter("bag_id", $bagId);
    $mawbParcelMappingObj = $mawbParcelMappingFilter->getColumnList("id,mawb_id");
    if(count($mawbParcelMappingObj) > 0){
// Already added
    }else{
        // Add data into mawb_parcel_mapping
        $mawbParcelMapping = new MawbParcelMapping();
        $mawbParcelMapping->setMawbId($mawbId);
        $mawbParcelMapping->setParcelId($parcelId);
        $mawbParcelMapping->setBagId($bagId);
        $mawbParcelMapping->setDateAdded(time());
        $mawbParcelMapping->setAddedBy($user->getId());
        $mawbParcelMapping->save();
    }
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
    // Add data into flightinfo_mapping
    // check if data already added into
    $flightinfoMappingFilter = new FlightMappingFilter();
    $flightinfoMappingFilter->addFieldFilter("    flight_info_id", $flightId);
    $flightinfoMappingFilter->addFieldFilter("    mawb_id", $mawbId);
    $flightinfoMappingObj = $flightinfoMappingFilter->getColumnList("id,mawb_id,is_delete");
    if (count($flightinfoMappingObj) > 0) {
        $flightinfoMappingObj[0]->getIsDelete();
        if($flightinfoMappingObj[0]->getIsDelete() == 1){
            $flightinfoMapping = new FlightMapping($flightinfoMappingObj[0]->getId());
            $flightinfoMapping->setIsDelete(0);
            $flightinfoMapping->save();
        }
    } else {
        $flightinfoMapping = new FlightMapping();
        $flightinfoMapping->setFlightInfoId($flightId);
        $flightinfoMapping->setMawbId($mawbId);
        $flightinfoMapping->setIsDelete(0);
        $flightinfoMapping->save();
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
    $user = SessionManager::getUser();
    $dataArr = [];
    if($bagId > 0){
        $bagging = new Bagging($bagId);
        $dataArr['bag_number'] = $bagging->getBagnumber();
        $dataArr['bag_weight'] = $bagging->getWeight();
        $dataArr['bag_actual_weight'] = $bagging->getActualWeight();
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
        }
        if ($addressline1 != '') {
            $y += 5;
            $pdf->Text(12, $y, strtoupper($addressline1));
        }
        if ($addressline2 != '') {
            $y += 5;
            $pdf->Text(12, $y, strtoupper($addressline2));
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
        }
        if ($postcode != '') {
            $y += 5;
            $pdf->Text(12, $y, strtoupper($postcode));
        }
        if ($countryname != '') {
            $y += 5;
            $pdf->Text(12, $y, strtoupper($countryname));
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
        $pdf->Text(20, 108, $dataArr['bag_service']);
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
    }
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
            $invoiceLink = $glOrderPdf->AddHTML($consignment,'', false, false, false);
            $pdf2->addPDF($invoiceLink, 'all');
            $conLabel = explode(".",$consignment->getLabelFile());
            /*$labelLinl  = "../_assets/pdf/".$conLabel[0].".pdf";
            if(file_exists($labelLinl)){
            try{
                $pdf2->addPDF($labelLinl, 'all');
            }catch (Exception $e){

            }
        }*/
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
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$countMawbDataLine, $mawbData['flight_number'])
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
        $objPHPExcel->getActiveSheet()->setTitle('Export Manifest');
        $fileName = "export_manifest/export-".strtoupper($HLValue)."-manifest-" . time(). rand('1', '10000') . ".xlsx";
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
                                                            $consignment->getNotes(),
                                                            $parcel->getParcelStatusCode()
                                                        ];
                    }
                }
            //}
        }
    }
    return $parcelDetails;
}
function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message,$mawbEmailCc) {
    $file = $path.$filename;
    //Create a new PHPMailer instance
    $mail = new PHPMailer;
    //Set who the message is to be sent from
    $mail->setFrom($from_mail, $from_name);
    //Set an alternative reply-to address
    $mail->addReplyTo($replyto);
    //Set who the message is to be sent to
    $receipts_email = explode(",",$mailto);
    foreach($receipts_email as $to)
    {
        $mail->addAddress($to, '');
    }
    $receipts_email_cc = explode(",",$mawbEmailCc);
    foreach($receipts_email_cc as $cc)
    {
        $mail->AddCC($cc, '');
    }
    $mail->addBCC("itsupport@oneworldexpress.com",'');
    //Set the subject line
    $mail->Subject = $subject;
    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body
    //$mail->msgHTML(file_get_contents('mm294882.c98'), '/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/eurobtoc_booking/czech_files_int/');
    //Replace the plain text body with one created manually
    $mail->AltBody = $message;
    $mail->Body = $message;

    //Attach an image file
    $mail->addAttachment($file);

    //send the message, check for errors
    if (!$mail->send()) {
        return  $mail->ErrorInfo;
    } else {
        return "success";
    }
}
?>