<?php

/**
 * Parcelgroupconsignment - Parcel group consignment class
 * - deals with Parcel group consignments
 *
 */

//ini_set('default_socket_timeout', '300');
include_classes([
    'consignmentdropoffmapping.class',
    'consignmentdropoffmappingfilter.class'
]);
class Tracking extends DbAccess3
{
    public $consignment;
    public $password;
    public $carrier;
    Private $trackingEvents = [];

    public static $oneworld_status_code = array(
        111 => "Out for delivery",
        112 => "Address Problem",
        113 => "Awaiting",
        114 => "Collection",
        115 => "Problem",
        116 => "Consignee Unavailable",
        117 => "Customs clearance",
        118 => "Damaged Parcel",
        119 => "Damaged",
        120 => "Delayed",
        121 => "Delivered",
        //122 => "Delivered",
        123 => "Delivery Attempt",
        124 => "Pending",
        125 => "Undelivered",
        126 => "Dispatched",
        127 => "Failure",
        128 => "Held",
        //129 => "In Transit But Delayed",
        130 => "Label Problem",
        131 => "Not Picked Up",
        133 => "Picked Up",
        134 => "Partially Collected",
        //135 => "Pending",
        136 => "Hold",
        137 => "In Transit",
        138 => "Returned",
        140 => "Collected",
        141 => "Dimensions Check",
        //142 => "Problem",
        //143 => "Returned",
        144 => "Data Received",
        145 => "Departed",
        146 => "Arrived",
        147 => "Partial Delivery",
        148 => "Carrier Received",
        149 => "Arrived at destination",
        150 => "Misroute",
        151 => "Offload",
        152 => "Destroyed",
        153 => "Seize",
        154 => "Carded",
        155 => "Others",
        156 => "Forwarded",
        157 => "Lost", 
        158 => "Data Problem",
        159 => "Drop Off"        
    );
    public static $oneworld_status_desc = array(
        111 => "Out for delivery",
        112 => "Address Problem",
        113 => "Awaiting",
        114 => "Collection",
        115 => "Problem",
        116 => "Consignee Unavailable",
        117 => "Customs clearance",
        118 => "Damaged Parcel but Out for delivery",
        119 => "Damaged",
        120 => "Delayed",
        121 => "Delivered",
      //  122 => "Delivered",
        123 => "Delivery Check",
        124 => "Delivery pending",
        125 => "Undelivered",
        126 => "Dispatched",
        127 => "Failure",
        128 => "Held",
        129 => "In transit but delayed.",
        130 => "Label Problem",
        131 => "Not Picked Up",
        133 => "Parcel Picked Up",
        134 => "Partially Collected",
        135 => "Pending",
        136 => "Please contact Customer Service.",
        137 => "In Transit",
        138 => "Returned",
        140 => "Collected",
        141 => "Dimensions Check",
        142 => "Dimesnions Problem",
        143 => "Return Item Authorised for Coll.",
        144 => "Parcel data received awaiting coll.",
        145 => "Departed",
        146 => "Arrived",
        147 => "Partial Delivery",
        148 => "Received at carrier hub",
        149 => "Arrived at destination",
        150 => "Misroute",
        151 => "Offload",
        152 => "Destroyed",
        153 => "Seize",
        154 => "Carded",
        155 => "Others",
        156 => "Forwarded",
        157 => "Lost",
        158 => "Data Problem",
        159 => "Drop Off"
    );
    public static $oneworld_consignment_code_mapping = array(
        111 => Consignment::STATUS_INTRANSIT,
        112 => Consignment::STATUS_PROBLEM,
        113 => Consignment::STATUS_INTRANSIT,
        114 => Consignment::STATUS_RECEIVED,
        115 => Consignment::STATUS_PROBLEM,
        116 => Consignment::STATUS_PROBLEM,
        117 => Consignment::STATUS_INTRANSIT,
        118 => Consignment::STATUS_DISCREPANCY,
        119 => Consignment::STATUS_DISCREPANCY,
        120 => Consignment::STATUS_INTRANSIT,
        121 => Consignment::STATUS_DELIVERED,
       // 122 => Consignment::STATUS_DELIVERED,
        123 => Consignment::STATUS_INTRANSIT,
        124 => Consignment::STATUS_INTRANSIT,
        125 => Consignment::STATUS_INTRANSIT,
        126 => Consignment::STATUS_DISPATCHED,
        127 => Consignment::STATUS_PROBLEM,
        128 => Consignment::STATUS_HOLD,
        129 => Consignment::STATUS_INTRANSIT,
        130 => Consignment::STATUS_PROBLEM,
        131 => Consignment::STATUS_RECEIVED,
        133 => Consignment::STATUS_INTRANSIT,
        134 => Consignment::STATUS_PARTIAL_RECEIVED,
        135 => Consignment::STATUS_INTRANSIT,
        136 => Consignment::STATUS_HOLD,
        137 => Consignment::STATUS_INTRANSIT,
        138 => Consignment::STATUS_RECEIVED,
        140 => Consignment::STATUS_INTRANSIT,
        141 => Consignment::STATUS_INTRANSIT,
        142 => Consignment::STATUS_PROBLEM,
        143 => Consignment::STATUS_RETURNED,
        144 => Consignment::STATUS_LABEL_CREATED,
        145 => Consignment::STATUS_DISPATCHED,
        146 => Consignment::STATUS_RECEIVED,
        147 => Consignment::STATUS_PARTIAL_DELIVERED,
        148 => Consignment::STATUS_INTRANSIT,
        149 => Consignment::STATUS_INTRANSIT,
        150 => Consignment::STATUS_INTRANSIT,
        151 => Consignment::STATUS_INTRANSIT,
        152 => Consignment::STATUS_PROBLEM,
        153 => Consignment::STATUS_PROBLEM,
        154 => Consignment::STATUS_DELIVERED,
        155 => Consignment::STATUS_INTRANSIT,
        156 => Consignment::STATUS_INTRANSIT,
        157 => Consignment::STATUS_PROBLEM,
        158 => Consignment::STATUS_LABEL_CREATED,
        159 => Consignment::STATUS_INTRANSIT,
    );

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct()
    {

    }

    public function GetTracking($trackingNumber, $format = "", $debug = false)
    {
        $trackingNumber = ParseTrackingNumber::Parse($trackingNumber);
        $trackingArray = [];
        $trackingDataFilter = new TrackingDataFilter();
        $trackingDataFilter->addTrackingNumberFilter($trackingNumber);
        $trackingDataFilter->AddOrderByDate(false);
        $trackingDataObj = $trackingDataFilter->getColumnList("*");
        if (count($trackingDataObj) > 0) {
            $entityType = $trackingDataObj[0]->getEntityType();
            $entityId = $trackingDataObj[0]->getEntityId();
            $consignmentId = 0;
            if ($entityType == 'parcel') {
                $parcelObj = new Parcel($entityId);
                $consignmentId = $parcelObj->getConsignmentId();
            } else {
                $consignmentId = $entityId;
            }
            $consignmentObj = new Consignment($consignmentId);
			$shipmentStatus = $consignmentObj->getShipmentStatus();
			if($shipmentStatus != Consignment::STATUS_DELIVERED) {
				$this->updateCarrierTracking($trackingNumber, $consignmentObj);
			}
            $trackingArray = $this->getTrackingDataArray($trackingNumber, $consignmentObj);
            //Update Tracking data
            /*
             * Hadi code start
             * First check if consignment is dispatch move with it
             * But if consignment is drop off then
             * Get dispatch consignment id of dispatch from consignment_dropoff_mapping and move with it
             * And found tracking number of that parcels from parcel_tracking column
             */
//            $firstTransitTime = (isset($trackingArray['shipment_detail']['transit_time']) ? $trackingArray['shipment_detail']['transit_time'] : "0000-00-00 00:00:00");
			$consignmentDropoffMappingFilter = new ConsignmentDropoffMappingFilter();
			//$consignmentDropoffMappingFilter->addFieldFilter("     dropoff_consignment_id",$consignmentId);
			$consignmentDropoffMappingFilter->addFilter("     (dropoff_consignment_id = '".DbAccess3::escape($consignmentId)."' OR dispatch_consignment_id = '".DbAccess3::escape($consignmentId)."')");
			$consignmentDropoffMappingData = $consignmentDropoffMappingFilter->getColumnList("dispatch_consignment_id,dropoff_consignment_id,parcel_tracking,bag_id,bag_number");
			if(count($consignmentDropoffMappingData) > 0) {
				$consignmentDropoffMappingData = $consignmentDropoffMappingData[0];
				$linkDispatchConsignmentId = $consignmentDropoffMappingData->getDispatchConsignmentId();
				$linkDropoffConsignmentId = $consignmentDropoffMappingData->getDropoffConsignmentId();
				$bagId = $consignmentDropoffMappingData->getBagId();
				$bagNumber = $consignmentDropoffMappingData->getBagNumber();
				$parcelData = json_decode($consignmentDropoffMappingData->getParcelTracking(), true);

				if($consignmentId == $linkDispatchConsignmentId){
					$linkConsignmentId = $linkDropoffConsignmentId;
					$tmpTrackingNos = array_keys($parcelData);
					$linkTrackingNumber = $tmpTrackingNos[0];
				}else if($consignmentId == $linkDropoffConsignmentId){
					$linkConsignmentId = $linkDispatchConsignmentId;
					$linkTrackingNumber = $parcelData[$trackingNumber];
				}

				$linkConsignmentObj = new Consignment($linkConsignmentId);
				$linkShipmentStatus = $consignmentObj->getShipmentStatus();
				if($linkShipmentStatus != Consignment::STATUS_DELIVERED) {
					$this->updateCarrierTracking($linkTrackingNumber, $linkConsignmentObj, true);
				}

				$this->trackingEvents = [];
				$linkTrackingArray = $this->getTrackingDataArray($linkTrackingNumber, $linkConsignmentObj);
				if(!empty($linkTrackingArray['shipment_detail']['events'])){
					foreach($linkTrackingArray['shipment_detail']['events'] as $linkEvent){
						if($linkEvent['event_code'] != '144' )
							$trackingArray['shipment_detail']['events'][] = $linkEvent;
					}
				}
				if(!empty($linkTrackingArray['tracking_events'])){
					foreach($linkTrackingArray['tracking_events'] as $linkTrackingEventDate => $linkTrackingEvents){
						foreach($linkTrackingEvents as $linkTrackingEvent) {
							if($linkTrackingEvent['status_code_id'] != '144')
								$trackingArray['tracking_events'][$linkTrackingEventDate][] = $linkTrackingEvent;
						}
					}
				}
//                $secondTransitTime = (isset($linkTrackingArray['shipment_detail']['transit_time']) ? $linkTrackingArray['shipment_detail']['transit_time'] : "0000-00-00 00:00:00");

                // If bag number or bag id not null
                if(!empty($bagId) && $bagId > 0 && !empty($bagNumber)){
                    $consignmentFilterNew = new ConsignmentFilter();
                    $consignmentFilterNew->addFieldFilter("    awb",$bagNumber);
                    $consignmentNewObj = $consignmentFilterNew->getColumnListLimit("*");
                    if(count($consignmentNewObj) > 0) {
                        $consignmentNewObj = $consignmentNewObj[0];
                        $newShipmentStatus = $consignmentNewObj->getShipmentStatus();
                        if ($newShipmentStatus != Consignment::STATUS_DELIVERED) {
                            $this->updateCarrierTracking($bagNumber, $consignmentNewObj, true);
                        }
                    }
                    $newTrackingArray = $this->getTrackingDataArray($bagNumber, $consignmentNewObj);
                    
                    if(!empty($newTrackingArray['shipment_detail']['events'])){
                        foreach($newTrackingArray['shipment_detail']['events'] as $linkEvent){
                            if($linkEvent['event_code'] == '144'  ){
                                continue;
                            }
                            else if(trim($linkEvent['event_code']) == '121'){
                                continue;
                            }
                            else{
                                $trackingArray['shipment_detail']['events'][] = $linkEvent;
                            }
                        }
                    }
                    if(!empty($newTrackingArray['tracking_events'])){
                        foreach($newTrackingArray['tracking_events'] as $linkTrackingEventDate => $linkTrackingEvents){
                            foreach($linkTrackingEvents as $linkTrackingEvent) {
                                if($linkTrackingEvent['status_code_id'] == '144'){
                                    continue;
                                }
                                else if($linkTrackingEvent['status_code_id'] == '121'){
                                    continue;
                                }
                                else{
                                    $trackingArray['tracking_events'][$linkTrackingEventDate][] = $linkTrackingEvent;
                                }
                            }
                        }
                    }
                }
			}
//            $thirdTransitTime = (isset($newTrackingArray['shipment_detail']['transit_time']) ? $newTrackingArray['shipment_detail']['transit_time'] : "0000-00-00 00:00:00");
//            $tmstamp = strtotime($firstTransitTime)+strtotime($secondTransitTime)+strtotime($thirdTransitTime);
//            date("Y m d", $tmstamp) ;
			// sort events
			$eventDateTimeSort = [];
			foreach ($trackingArray['shipment_detail']['events'] as  $event) {
				$eventDateTimeSort[$event['event_datetime']] = $event;
			}
			array_multisort($eventDateTimeSort, SORT_DESC, $trackingArray['shipment_detail']['events']);
			// Update last event afer sort
			$trackingArray['shipment_detail']['last_event'] = $trackingArray['shipment_detail']['events'][0];
			// sort tracking events
			$dateSort = [];
			$dateTimeSort = [];
                        
			foreach ($trackingArray['tracking_events'] as $key => $row) {
                            $row = array_map("unserialize", array_unique(array_map("serialize", $row)));
                            $trackingArray['tracking_events'][$key] = array_map("unserialize", array_unique(array_map("serialize", $trackingArray['tracking_events'][$key])));
				foreach ($row as $one) {
					$dateSort[$key] = $one;
					$dateTimeSort[$one['date_time']] = $one;
				}
				array_multisort($dateTimeSort, SORT_DESC, $trackingArray['tracking_events'][$key]);
			}
			array_multisort($dateSort, SORT_DESC, $trackingArray['tracking_events']);
                 
            /*
             * Hadi code End
             */
           
        } else {
            $consignmentFilter = new ConsignmentFilter();
            //$trackingNumber = htmlspecialchars(trim($trackingNumber));
            $consignmentFilter->addFilter("    ((c.awb = '" . DbAccess3::escape($trackingNumber) . "' AND c.awb != '') OR (c.hawb = '" . DbAccess3::escape($trackingNumber) . "' AND c.hawb != ''))", "filter");
            $consignmentList = $consignmentFilter->getListNew("awb, hawb, service_id, agent_id, c.country_id, c.shipment_status, c.consignment_status, c.postcode, c.address_line_1, c.address_line_2, c.address_line_3, c.city, c.state, c.number_pieces, c.date_created");
            $relabelFilter = new ConsignmentRelabelFilter();
            $relabelFilter->addOldParcelTrackingNoFindInSetFilter($trackingNumber);
            $relabelList = $relabelFilter->getList();
            if (count($consignmentList) > 1) { // if two records
                $filter = new ConsignmentFilter();
                $filter->addAwbAndHawbOrFilterNotRecycled($trackingNumber);
                $consignmentList = $filter->getListNew("awb, hawb, service_id, agent_id, c.country_id, c.shipment_status, c.consignment_status, c.postcode, c.address_line_1, c.address_line_2, c.address_line_3, c.city, c.state, c.number_pieces, c.date_created");

            } else if ((count($consignmentList) == 0 || count($consignmentList) > 0) && count($relabelList) > 0) {
                $relabelObj = $relabelList[0];
                $oldTrackingNumberArr = json_decode($relabelObj->getOldNewTrackingMapping());
                $trackingNumber = ParseTrackingNumber::Parse($oldTrackingNumberArr->$trackingNumber);
                if (empty($trackingNumber)) {
                    $trackingNumber = ParseTrackingNumber::Parse($relabelObj->getNewTrackingNo());
                }
                $filter = new ConsignmentFilter();
                $filter->addAwbAndHawbOrFilterNotRecycled($trackingNumber);
                $consignmentList = $filter->getListNew("awb, hawb, service_id, agent_id, c.country_id, c.shipment_status, c.consignment_status, c.postcode, c.address_line_1, c.address_line_2, c.address_line_3, c.city, c.state, c.number_pieces, c.date_created");
            }
            $consignmentInformation = '';
            if (count($consignmentList) > 0) {
                $consignmentInformation = $consignmentList[0];
                $trackingNumber = trim(ParseTrackingNumber::Parse($consignmentInformation->getAwb()));
                if ($trackingNumber == '')
                    $trackingNumber = trim(ParseTrackingNumber::Parse($consignmentInformation->getHawb()));
            } else {
                $parcelFilter = new ParcelFilter();
                $parcelFilter->addTrackingNumberFilter($trackingNumber);
                $parcelList = $parcelFilter->getColumnList("id, consignment_id, tracking_number");
                if (count($parcelList) > 0) {
                    $parcel = $parcelList[0];
                    $consignmentId = $parcel->getConsignmentId();
                    $consignmentInformation = new Consignment($consignmentId);
                    $trackingNumber = trim(ParseTrackingNumber::Parse($consignmentInformation->getAwb()));
                    if ($trackingNumber == '')
                        $trackingNumber = trim(ParseTrackingNumber::Parse($consignmentInformation->getHawb()));
                }
            }
            if (!empty($consignmentInformation)) {
                //Update Tracking data
                $this->updateCarrierTracking($trackingNumber, $consignmentInformation);
                /////
                $trackingDataFilter = new TrackingDataFilter();
                $trackingDataFilter->addTrackingNumberFilter($trackingNumber);
                $trackingDataFilter->AddOrderByDate(false);
                $trackingDataObj = $trackingDataFilter->getColumnList("*");
                //if(count($trackingDataObj) > 0)
                    $trackingArray = $this->getTrackingDataArray($trackingNumber, $consignmentInformation);
            }
        }
        $output = [];
        if (empty($trackingArray)) {
            $output['status'] = 'error';
            $output['message'] = 'No Tracking data found';
        } else {
            $output['status'] = 'success';
            $output['message'] = 'Tracking data found';
            $output['tracking'] = $trackingArray;
        }
        if ($format == 'json')
            return json_encode($output);
        else
            return $output;
    }

    public function GetMultiTracking($trackingNumbers)
    {
        $trackingArray = [];
        $warnings = [];
        if(!empty($trackingNumbers)) {
            foreach ($trackingNumbers as $number) {
                $valid = $this->validateMultiTrackingParams(['tracking_number' => $number]);
                if(empty($valid)) {
                    $trackingData = $this->GetTracking($number);
                    if(!empty($trackingData['tracking'])) {
                        $trackingArray[] = $trackingData['tracking'];
                    } else {
                        $warnings[] = 'Tracking data of tracking number ' . $number . ' not found.';
                    }
                } else {
                    $warnings[] = $valid;
                }
            }
        }
        $output = [];
        if (empty($trackingArray)) {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = 'No Tracking data found.';
            $output['ERROR'] = ['No Tracking data found.'];
        } else {
            $output['STATUS'] = 'SUCCESS';
            $output['MESSAGE'] = 'Tracking data found';
            $output['tracking'] = $trackingArray;
            $output['ERROR'] = $warnings;
        }
        return $output;
    }

    public function validateMultiTrackingParams($params)
    {
        $defaultParams = [
            'tracking_number' => 32,
        ];
        $invalidParams = '';
        foreach ($defaultParams as $key => $value) {
            if(is_integer($value) && strlen($params[$key]) > $value) {
                $invalidParams = $key . ' length is greater than maximum character limit. Maximum character limit is ' . $value;
            }
        }
        return $invalidParams;
    }
    
    public static function setScanDate($ConsignmentObj)
    {
        $trackingDataFilter = new TrackingDataFilter();
        $trackingDataFilter->addFieldFilter('    tracking_number',$ConsignmentObj->getAwb());
        $trackingDataFilter->addFilter("   status_code_id not in ('144','158') AND carrier_code is not null AND warehouse_id = 0");
        $trackingDataExistsObj = $trackingDataFilter->getColumnList("t.id, t.date_created");

        if(count($trackingDataExistsObj) > 0)
        {
            $ConsignmentObj->setDateScanned(strtotime($trackingDataExistsObj[0]->getDateCreated()));
            $ConsignmentObj->eventKey = 'scanDate';
            $ConsignmentObj->save();
        }
    }
    
    public function saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode)
    {
       // echo $EventCode; 
        if($finalStatusCode == Consignment::STATUS_DELIVERED)
        {
            return true;
        }
        
        $trackingDataFilter = new TrackingDataFilter();
        $trackingDataFilter->addTrackPointExistFilter($trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription,$DateTime);
            $trackingDataExistsObj = $trackingDataFilter->getColumnList('t.id');
        
        if (count($trackingDataExistsObj) == 0) 
        {
            $trackingData = [
                'user_id' => 0,
                'entity_id' => $entityId,
                'entity_type' => $trackBy,
                'tracking_number' => $trackingNumber,
                'track_point' => $ServiceAreaDescription,
                'date_created' => $DateTime,
                'ip_address' => getClientIp(),
                'status_code_id' => $spTrackingStatus,
                'carrier_code' => $EventCode,
                'carrier_desc' => $EventDescription,
                'signatory' => $Signatory
            ];
            $trackingDataObj = new TrackingData($trackingData);			
            $trackingDataObj->save();
        }
        
        if(in_array($EventCode, $deliveredArray))
        {
            $finalStatusCode = Consignment::STATUS_DELIVERED;


         //   $this->saveConsignmentTrackingStatus($trackingNumber, 'YodelTrackingStatus');
            return true;      
        } 

    }
    
    public function saveConsignmentTrackingStatus($trackingNumber, $className)
    {
        $trackingDataFilter = new TrackingDataFilter();
        $trackingDataFilter->addTrackingNumberFilter($trackingNumber);
        $trackingDataFilter->AddOrderByDate(false);
        $trackingDataObj = $trackingDataFilter->getColumnList('entity_id,entity_type,carrier_code,status_code_id,date_created','',1);
        
        if (count($trackingDataObj) > 0) 
        {
            $trackingDataObj = $trackingDataObj[0];            
            $carrierCode = $trackingDataObj->getCarrierCode();
            $oweTrackingStatusCode = $trackingDataObj->getStatusCodeId();
            $entityType = $trackingDataObj->getEntityType();
            $entityId = $trackingDataObj->getEntityId();
            $consignmentStatusCode = $className::getConsignmentStatus($oweTrackingStatusCode);
            //echo $consignmentStatusCode; die;
            $consignmentId = 0;
            if ($entityType == 'parcel') 
            {
                $parcelObj = new Parcel($entityId);
                $dateTime = date("Y-m-d H:i:s");
                $parcelObj->setParcelStatusCode($consignmentStatusCode);
               // $parcelObj->setLastTrackingUpdate(strtotime($dateTime));
                $parcelObj->save();
                $consignmentId = $parcelObj->getConsignmentId();
            } 
            else 
            {
                $consignmentId = $entityId;
                $parcelObj = new Parcel();
                $parcelObj->bulkUpdate("parcel_status_code='" . $consignmentStatusCode . "'", "consignment_id = '" . $consignmentId . "'");
            }
            $ConsignmentObj = new Consignment($consignmentId);
            $consignmentStatus = isset(Consignment::$database_status_array[$consignmentStatusCode]) ? Consignment::$database_status_array[$consignmentStatusCode] : '';
            
            
            
            if($ConsignmentObj->getShipmentType() == "DO"){
                $consignmentDropoffMappingFilter = new ConsignmentDropoffMappingFilter();
                $consignmentDropoffMappingFilter->addFieldFilter("     dropoff_consignment_id",$consignmentId);
                $consignmentDropoffMappingData = $consignmentDropoffMappingFilter->getColumnList("dispatch_consignment_id,parcel_tracking");
                
                // International Drop off
                if(count($consignmentDropoffMappingData) > 0){
                    $consignmentDropoffMappingData = $consignmentDropoffMappingData[0];
                    $desctinationConsignmentId = $consignmentDropoffMappingData->getDispatchConsignmentId();
                    $parcelData = json_decode($consignmentDropoffMappingData->getParcelTracking(),true);
                    $trackingNumber = $parcelData[$trackingNumber];
                }
                //Domestic Drop off
                else
                {
                    $desctinationConsignmentId = $consignmentId; 
                }
                // end of domestic drop off
                
                $destinationConsignmentObj = new Consignment($destinationConsignmentId);
                $destinationShipmentStatus = $destinationConsignmentObj->getShipmentStatus();                
                
                if($destinationShipmentStatus == Consignment::STATUS_DELIVERED)
                {
                    $destinationConsignmentObj->setShipmentStatus(Consignment::STATUS_DELIVERED);
                    $destinationConsignmentObj->setDateDelivered(strtotime($trackingDataObj->getDateCreated()));
                    $consignmentObj->setShipmentStatus(Consignment::STATUS_DELIVERED);
                    $consignmentObj->setDateDelivered(strtotime($trackingDataObj->getDateCreated()));
                }
            }
            else 
            {
                $ConsignmentObj->setShipmentStatus($consignmentStatusCode);
                if($consignmentStatusCode == Consignment::STATUS_DELIVERED)
                {
                    $ConsignmentObj->setDateDelivered(strtotime($trackingDataObj->getDateCreated()));
                }
                if ($consignmentStatus != '')
                $ConsignmentObj->setConsignmentStatus($consignmentStatus);            
            }
            
            if(empty($ConsignmentObj->getDateScanned()))
            {
                Tracking::setScanDate($ConsignmentObj);
            }

            $ConsignmentObj->save();
        }
    }
    
    private function getTrackingDataArray($trackingNumber, Consignment $consignmentObj)
    {
        $trackingArray = [];
        $trackingDataFilter = new TrackingDataFilter();
        $trackingDataFilter->addTrackingNumberFilter($trackingNumber);
        $trackingDataFilter->AddOrderByDate(false);
        $trackingDataObj = $trackingDataFilter->getColumnList("*");
        $parcelLength = "";
        $parcelWidth = "";
        $parcelHeight = "";
        $parcelWeight = "";
        $carrierCode = "";
        $carrierStatusDesc = "";
        $lastTrackingEvent = [];
        if(!empty($trackingDataObj)){
            $carrierCode = $trackingDataObj[0]->getStatusCodeId();
            $carrierStatusDesc = $trackingDataObj[0]->getCarrierDesc();
            $lastTrackingEvent = [
                                    'event_datetime' => $trackingDataObj[0]->getDateCreated(),
                                    'event_code' => $trackingDataObj[0]->getStatusCodeId(),
                                    'event_desc' => $trackingDataObj[0]->getCarrierDesc(),
                                    'city' => $consignmentObj->getCity(),
                                    'state' => $consignmentObj->getState(),
                                    'country' => Country::nameCountry($consignmentObj->getCountryId()),
                                    'company' => $consignmentObj->getCompany(),
                                    'signer' => $trackingDataObj[0]->getSignatory(),
                                    'geo' => [
                                        'lat' => $trackingDataObj[0]->getLatitude(),
                                        'long' => $trackingDataObj[0]->getLongitude(),
                                    ]
                                ];

            $parcelDataObj = new Parcel($trackingDataObj[0]->getEntityId());
            if(!empty($parcelDataObj)){
                $parcelLength = $parcelDataObj->getLength();
                $parcelWidth = $parcelDataObj->getWidth();
                $parcelHeight = $parcelDataObj->getHeight();
                $parcelWeight = $parcelDataObj->getweight();
            }
        }
        $countryObj = new Country($consignmentObj->getCountryId());
        $userId = $consignmentObj->getUserId();
        $servicesObj = new Services($consignmentObj->getServiceId());
        $carrierObj = new Carrier($servicesObj->getCarrierId());
        $transitTimeObj = new ServiceCountryTimeFilter();
        $transitTimeObj->addFilter(' id_country = ' . $consignmentObj->getCountryId());
        $transitTimeObj->addFilter(' id_service = ' . $consignmentObj->getServiceId());
        $transitTimeObj = $transitTimeObj->getColumnList(' transit_time ');
        $transitTime = 0;
        if(count($transitTimeObj) > 0)
        {
            $transitTime = $transitTimeObj[0]->getTransitTime();
        }
        $trackingArray = [
            'shipment_detail' => [
                'tracking_number' => $trackingNumber,
                'company' => $consignmentObj->getCompany(),
                'service' => $servicesObj->getName(),
                'service_code' => $servicesObj->getCode(),
                'carrier_name' => $carrierObj->getCarrier(),
                'carrier_code' => $carrierCode,
                'carrier_status_desc' => $carrierStatusDesc,
                'shipped_datetime' => $consignmentObj->getDateCreated(),
                'estimate_delivery_time' => $transitTime,
                'hawb' => $consignmentObj->getHawb(),
                'origin_country' => $this->originCountryFromUser($userId),
                'destination_country' => $countryObj->getName(),
                'address_1' => $consignmentObj->getAddressLine1(),
                'address_2' => $consignmentObj->getAddressLine2(),
                'address_3' => $consignmentObj->getAddressLine3(),
                'city' => $consignmentObj->getCity(),
                'state' => $consignmentObj->getState(),
                'postcode' => $consignmentObj->getPostcode(),
                'number_pieces' => $consignmentObj->getNumberPieces(),
                'status' => Consignment::$status_array[$consignmentObj->getShipmentStatus()],
                'status_code' => $consignmentObj->getShipmentStatus(),
                'parcel_length' => $parcelLength,
                'parcel_width' => $parcelWidth,
                'parcel_height' => $parcelHeight,
                'parcel_weight' => $parcelWeight,
                'last_event' => $lastTrackingEvent,
                'events' => []
            ]
        ];
        foreach ($trackingDataObj as $trackingData) {
			$trackingPracelImage = "";
			$trackingSignatoryImage = "";
			if (!empty($trackingData->getParcelImage())) {
				$trackingPracelImage = SETTING_MAIN_ASSETS . "images/pod_images/" . $trackingData->getParcelImage();
			}
			if (!empty($trackingData->getPodImage())) {
				$trackingSignatoryImage = SETTING_MAIN_ASSETS . "images/pod_images/" . $trackingData->getPodImage();
			}
            $trackingArray['shipment_detail']['events'][] = [
                'event_datetime' => $trackingData->getDateCreated(),
                'event_code' => $trackingData->getStatusCodeId(),
                'event_desc' => $trackingData->getCarrierDesc(),
                'city' => $consignmentObj->getCity(),
                'state' => $consignmentObj->getState(),
                'country' => Country::nameCountry($consignmentObj->getCountryId()),
                'company' => $consignmentObj->getCompany(),
                'signer' => $trackingData->getSignatory(),
				'pod_image' => $trackingSignatoryImage,
				'parcel_image' => $trackingPracelImage,
                'geo' => [
                    'lat' => $trackingData->getLatitude(),
                    'long' => $trackingData->getLongitude(),
                ]
            ];
        }
        $startDate = '';
        $endDate = '';
        $startEndDate = [];
        if (count($trackingDataObj) > 0) {
            $startEndDate = $this->trackingDataEvent($trackingDataObj);
            /* if shipnment is dropoff then tracking to end level*/
            /*if ($consignmentObj->getShipmentType() == "DO") {
                $dParcelFilter = new ParcelFilter();
                $dParcelFilter->addFieldFilter("    hawb", $consignmentObj->getAwb());
                $dParcelFilterObj = $dParcelFilter->getList("*");
                if (count($dParcelFilterObj) > 0) {
                    $dTrackingDataFilter = new TrackingDataFilter();
                    $dTrackingDataFilter->addTrackingNumberFilter($dParcelFilterObj[0]->getTrackingNumber());
                    $dTrackingDataFilter->AddOrderByDate(false);
                    $dTrackingDataObj = $dTrackingDataFilter->getColumnList("*");
                    if (count($dTrackingDataObj) > 0) {
                        $startEndDate = $this->trackingDataEvent($dTrackingDataObj);
                    }
                }
            }*/
        }
        $startDate = (isset($startEndDate['startDate']) ? $startEndDate['startDate'] : '');
        $endDate = (isset($startEndDate['endDate']) ? $startEndDate['endDate'] : '');
        $trackingArray['shipment_detail']['transit_time'] = $this->datetimeInterval($startDate, $endDate);
        $trackingArray += ['tracking_events' => $this->trackingEvents];
        return $trackingArray;
    }

    private function getMultiTrackingDataArray($trackingNumber, Consignment $consignmentObj)
    {
        $trackingDataFilter = new TrackingDataFilter();
        $trackingDataFilter->addTrackingNumberFilter($trackingNumber);
        $trackingDataFilter->AddOrderByDate(false);
        $trackingDataObj = $trackingDataFilter->getColumnList("*");
        $parcelLength = "";
        $parcelWidth = "";
        $parcelHeight = "";
        $parcelWeight = "";
        $carrierCode = "";
        $carrierStatusDesc = "";
        $lastTrackingEvent = [];
        if(!empty($trackingDataObj)){
            $carrierCode = $trackingDataObj[0]->getStatusCodeId();
            $carrierStatusDesc = $trackingDataObj[0]->getCarrierDesc();
			$trackingPracelImage = "";
			$trackingSignatoryImage = "";
			if (!empty($trackingDataObj[0]->getParcelImage())) {
				$trackingPracelImage = SETTING_MAIN_ASSETS . "images/pod_images/" . $trackingDataObj[0]->getParcelImage();
			}
			if (!empty($trackingDataObj[0]->getPodImage())) {
				$trackingSignatoryImage = SETTING_MAIN_ASSETS . "images/pod_images/" . $trackingDataObj[0]->getPodImage();
			}
            $lastTrackingEvent = [
                'event_datetime' => $trackingDataObj[0]->getDateCreated(),
                'event_code' => $trackingDataObj[0]->getStatusCodeId(),
                'event_desc' => $trackingDataObj[0]->getCarrierDesc(),
                'city' => $consignmentObj->getCity(),
                'state' => $consignmentObj->getState(),
                'country' => Country::nameCountry($consignmentObj->getCountryId()),
                'company' => $consignmentObj->getCompany(),
                'signer' => $trackingDataObj[0]->getSignatory(),
				'pod_image' => $trackingSignatoryImage,
				'parcel_image' => $trackingPracelImage,
                'geo' => [
                    'lat' => $trackingDataObj[0]->getLatitude(),
                    'long' => $trackingDataObj[0]->getLongitude(),
                ]
            ];

            $parcelDataObj = new Parcel($trackingDataObj[0]->getEntityId());
            if(!empty($parcelDataObj)){
                $parcelLength = $parcelDataObj->getLength();
                $parcelWidth = $parcelDataObj->getWidth();
                $parcelHeight = $parcelDataObj->getHeight();
                $parcelWeight = $parcelDataObj->getweight();
            }
        }
        $countryObj = new Country($consignmentObj->getCountryId());
        $userId = $consignmentObj->getUserId();
        $servicesObj = new Services($consignmentObj->getServiceId());
        $carrierObj = new Carrier($servicesObj->getCarrierId());
        $transitTimeObj = new ServiceCountryTimeFilter();
        $transitTimeObj->addFilter(' id_country = ' . $consignmentObj->getCountryId());
        $transitTimeObj->addFilter(' id_service = ' . $consignmentObj->getServiceId());
        $transitTimeObj = $transitTimeObj->getColumnList(' transit_time ');
        $transitTime = 0;
        if(count($transitTimeObj) > 0)
        {
            $transitTime = $transitTimeObj[0]->getTransitTime();
        }
        $trackingArray = [
            $trackingNumber => [
                'tracking_number' => $trackingNumber,
                'company' => $consignmentObj->getCompany(),
                'service' => $servicesObj->getName(),
                'service_code' => $servicesObj->getCode(),
                'carrier_name' => $carrierObj->getCarrier(),
                'carrier_code' => $carrierCode,
                'carrier_status_desc' => $carrierStatusDesc,
                'shipped_datetime' => $consignmentObj->getDateCreated(),
                'estimate_delivery_time' => $transitTime,
                'hawb' => $consignmentObj->getHawb(),
                'origin_country' => $this->originCountryFromUser($userId),
                'destination_country' => $countryObj->getName(),
                'address_1' => $consignmentObj->getAddressLine1(),
                'address_2' => $consignmentObj->getAddressLine2(),
                'address_3' => $consignmentObj->getAddressLine3(),
                'city' => $consignmentObj->getCity(),
                'state' => $consignmentObj->getState(),
                'postcode' => $consignmentObj->getPostcode(),
                'number_pieces' => $consignmentObj->getNumberPieces(),
                'status' => Consignment::$status_array[$consignmentObj->getShipmentStatus()],
                'status_code' => $consignmentObj->getShipmentStatus(),
                'parcel_length' => $parcelLength,
                'parcel_width' => $parcelWidth,
                'parcel_height' => $parcelHeight,
                'parcel_weight' => $parcelWeight,
                'last_event' => $lastTrackingEvent,
                'events' => []
            ]
        ];
        foreach ($trackingDataObj as $trackingData) {
			$trackingPracelImage = "";
			$trackingSignatoryImage = "";
			if (!empty($trackingData->getParcelImage())) {
				$trackingPracelImage = SETTING_MAIN_ASSETS . "images/pod_images/" . $trackingData->getParcelImage();
			}
			if (!empty($trackingData->getPodImage())) {
				$trackingSignatoryImage = SETTING_MAIN_ASSETS . "images/pod_images/" . $trackingData->getPodImage();
			}
            $trackingArray['shipment_detail']['events'][] = [
                'event_datetime' => $trackingData->getDateCreated(),
                'event_code' => $trackingData->getStatusCodeId(),
                'event_desc' => $trackingData->getCarrierDesc(),
                'city' => $consignmentObj->getCity(),
                'state' => $consignmentObj->getState(),
                'country' => Country::nameCountry($consignmentObj->getCountryId()),
                'company' => $consignmentObj->getCompany(),
                'signer' => $trackingData->getSignatory(),
				'pod_image' => $trackingSignatoryImage,
				'parcel_image' => $trackingPracelImage,
                'geo' => [
                    'lat' => $trackingData->getLatitude(),
                    'long' => $trackingData->getLongitude(),
                ]
            ];
        }
        $startDate = '';
        $endDate = '';
        $startEndDate = [];
        if (count($trackingDataObj) > 0) {
            $startEndDate = $this->trackingDataEvent($trackingDataObj);
            /* if shipnment is dropoff then tracking to end level*/
            if ($consignmentObj->getShipmentType() == "DO") {
                $dParcelFilter = new ParcelFilter();
                $dParcelFilter->addFieldFilter("    hawb", $consignmentObj->getAwb());
                $dParcelFilterObj = $dParcelFilter->getList("*");
                if (count($dParcelFilterObj) > 0) {
                    $dTrackingDataFilter = new TrackingDataFilter();
                    $dTrackingDataFilter->addTrackingNumberFilter($dParcelFilterObj[0]->getTrackingNumber());
                    $dTrackingDataFilter->AddOrderByDate(false);
                    $dTrackingDataObj = $dTrackingDataFilter->getColumnList("*");
                    if (count($dTrackingDataObj) > 0) {
                        $startEndDate = $this->trackingDataEvent($dTrackingDataObj);
                    }
                }
            }
        }
        $startDate = (isset($startEndDate['startDate']) ? $startEndDate['startDate'] : '');
        $endDate = (isset($startEndDate['endDate']) ? $startEndDate['endDate'] : '');
        $trackingArray['shipment_detail']['transit_time'] = $this->datetimeInterval($startDate, $endDate);
        $trackingArray += ['tracking_events' => $this->trackingEvents];
        return $trackingArray;
    }

    public function trackingDataEvent($trackingDataObj)
    {
        $startDate = '';
        $endDate = '';
        $trackingDates = [];
        foreach ($trackingDataObj as $tracking) {
            /*if($tracking->getEntityType() == 'parcel')
                $trackingArray['shipment_detail']['tracking_number'] = $tracking->getTrackingNumber();*/
            if ($startDate == '')
                $startDate = $tracking->getDateCreated();

            $endDate = $tracking->getDateCreated();
            $warehouseData = "";
            $trackingCityName = "";
            $trackingCountryName = "";
            if ($tracking->getWarehouseId() > 0)
                $warehouseData = $this->getWarehouseData($tracking->getWarehouseId());
            if (!empty($warehouseData)) {
                $trackingCountryId = $warehouseData->getCountryid();
                $country = new Country($trackingCountryId);
                if (!empty($country))
                    $trackingCountryName = $country->getName();
                $trackingCityName = $warehouseData->getCitytown();
            }
            $trackingPracelImage = "";
            $trackingSignatoryImage = "";
            if (!empty($tracking->getParcelImage())) {
                $trackingPracelImage = SETTING_MAIN_ASSETS . "images/pod_images/" . $tracking->getParcelImage();
            }
            if (!empty($tracking->getPodImage())) {
                $trackingSignatoryImage = SETTING_MAIN_ASSETS . "images/pod_images/" . $tracking->getPodImage();
            }
            $trackingEvent = [
                'date_time' => $tracking->getDateCreated(),
                'time_12_hr' => date('H:i', strtotime($tracking->getDateCreated())), // it was 12 hour setup as AM/PM but according to Yasmin request we changed to 24 hour clcok
                'track_point' => $tracking->getTrackPoint(),
                'event_content' => self::$oneworld_status_code[$tracking->getStatusCodeId()],
                'status_code_id' => $tracking->getStatusCodeId(),
                'carrier_desc' => $tracking->getCarrierDesc(),
                'signatory' => $tracking->getSignatory(),
                'pod_image' => $trackingSignatoryImage,
                'parcel_image' => $trackingPracelImage,
                'tracking_country' => $trackingCountryName,
                'tracking_city' => $trackingCityName,
				'geo' => [
					'lat' => $tracking->getLatitude(),
					'long' => $tracking->getLongitude(),
				]
            ];
            if(is_array($this->trackingEvents)) {
                foreach ($this->trackingEvents as $trackingEventCheck) {
                    if (in_array($tracking->getTrackPoint(), array_column($trackingEventCheck, 'track_point')) 
                            && in_array($tracking->getStatusCodeId(), array_column($trackingEventCheck, 'status_code_id'))) { // search value in the array
                        continue;
                     //   $trackingEvent = "";
                    }
                }
            }
            $this->trackingEvents[date('Y-m-d', strtotime($tracking->getDateCreated()))][] = $trackingEvent;
        }
        $trackingDates = ['startDate' => $startDate, 'endDate' => $endDate];
        return $trackingDates;
    }

    public function updateCarrierTracking($trackingNumber,$consignmentObj)
    {
        //////////////////// Code to update dispatch date or date booked if its null ///////////////////
        $consignmentFilter = new ConsignmentFilter();
        $consignmentFilter->addFieldFilter("c.awb",$trackingNumber);        
        $consignmentFilter->addJoin("tracking_data td",'td.tracking_number = c.awb');
        $consignmentFilter->addFieldFilter("td.tracking_number",$trackingNumber);
        $consignmentFilter->addFieldFilter("td.status_code_id", '148');
        $result = $consignmentFilter->getListNew('c.id, c.date_booked, td.date_created','',false);
        if(count($result)>0)
        {
            $dateBooked = $result[0]->getDateBooked();
            if($dateBooked == '0' || $dateBooked == '' || $dateBooked == null)
            {       
                $consignment = new Consignment($result[0]->getId());
                $consignment->setDateBooked(strtotime($result[0]->getDateCreated()));
                $consignment->save();            
            }
        }
        $className = '';
//        $serivceAgentMapping = new ServiceAgentMappingDataFilter();
//        $serivceAgentMapping->addFilter(" serviceid = '" . $serviceId . "' and agentid = '" . $agentId . "'");
//        $serviceAgentMappintRecordSet = $serivceAgentMapping->getList();
//        if (count($serviceAgentMappintRecordSet) > 0) {
//            foreach ($serviceAgentMappintRecordSet as $agentData) {
//                $className = $agentData->getClassFileName();
//            }
//        }
//        if (trim($className) == '') {
//            $serivces = new Services($serviceId);
//            $className = trim($serivces->getLabelClassName());
//        }
//
//        $file = strtolower("../includes/labels/$className.class.php");
//        if (file_exists($file)) {
//            require_once $file;
//            $classobj = new $className;
//            $classobj->tracking($trackingNumber);
//        }
        $className = Consignment::IncludeCarrierClass($consignmentObj);
		$trackBy = 'parcel';
		$parcelFilter = new ParcelFilter();
		$parcelFilter->addConsignmentIdFilter($consignmentObj->getId());
		$parcelFilter->addTrackingNumberFilter($trackingNumber);
		$parcelData = $parcelFilter->getColumnList("id");
		if(count($parcelData) == 0){
			$trackBy = 'shipment';
		}
		if($className !=false){
			$classobj = new $className;
			$classobj->tracking($trackingNumber,$trackBy);
		}
    }


    public function datetimeInterval($dateScanned = '', $deleveruDate = '', $trackingNumber = '')
    {

        if ($trackingNumber != '') {
            $dateScan = new TrackingDataFilter();
            $dateScan->addTrackingNumberFilter($trackingNumber);
            $dateScan->AddOrderByID('asc');
            $dateScanned = $dateScan->getColumnList('date_created');
            if (count($dateScanned) > 0)
                $dateScanned = $dateScanned[0]->getDateCreated();
        }
        //echo $dateScanned; die;
        if (trim($dateScanned) != '' && trim($dateScanned) != '0000-00-00 00:00:00') {
            $datetime1 = new DateTime($dateScanned);
            $datetime2 = new DateTime($deleveruDate);
            $interval = $datetime1->diff($datetime2);
            return $mailData = $interval->format('%d days  %h Hours'); //die;
        } else
            return '';
    }

    public function originCountryFromUser($userId)
    {
        $countryName = 'United Kingdom';
        if ($userId > 0) {
            $user = new User($userId);
            $countryId = $user->getCountryId();
            if ($countryId > 0) {
                $country = new Country($countryId);
                $countryName = $country->getName();
            }
        }
        return $countryName;
    }


    
    public function getWarehouseData($warehouseId)
    {
        $warehouse = "";
        if ($warehouseId > 0) {
            $warehouse = new Warehouse($warehouseId);
        }
        return $warehouse;
    }

    public function trackingDataEvents($trackingNumber)
    {
        $dTrackingDataFilter = new TrackingDataFilter();
        $dTrackingDataFilter->addTrackingNumberFilter($trackingNumber);
        $dTrackingDataFilter->AddOrderByDate(false);
        $dTrackingDataObj = $dTrackingDataFilter->getColumnList("*");
        $trackingEvents = [];
        $startDate = '';
        $endDate = '';
        $trackingDates = [];
        foreach ($dTrackingDataObj as $tracking) {
            /*if($tracking->getEntityType() == 'parcel')
                $trackingArray['shipment_detail']['tracking_number'] = $tracking->getTrackingNumber();*/
            if ($startDate == '')
                $startDate = $tracking->getDateCreated();

            $endDate = $tracking->getDateCreated();
            $warehouseData = "";
            $trackingCityName = "";
            $trackingCountryName = "";
            if ($tracking->getWarehouseId() > 0)
                $warehouseData = $this->getWarehouseData($tracking->getWarehouseId());
            if (!empty($warehouseData)) {
                $trackingCountryId = $warehouseData->getCountryid();
                $country = new Country($trackingCountryId);
                if (!empty($country))
                    $trackingCountryName = $country->getName();
                $trackingCityName = $warehouseData->getCitytown();
            }
            $trackingPracelImage = "";
            $trackingSignatoryImage = "";
            if (!empty($tracking->getParcelImage()) > 0) {
                $trackingPracelImage = SETTING_MAIN_ASSETS . "images/pod_images/" . $tracking->getParcelImage();
                $trackingSignatoryImage = SETTING_MAIN_ASSETS . "images/pod_images/" . $tracking->getPodImage();
            }
            $trackingEvent = [
                'tracking_data_id' => $tracking->getId(),
                'date_time' => $tracking->getDateCreated(),
                'time_12_hr' => date('H:i', strtotime($tracking->getDateCreated())), // it was 12 hour setup as AM/PM but according to Yasmin request we changed to 24 hour clcok
                'track_point' => $tracking->getTrackPoint(),
                'event_content' => self::$oneworld_status_code[$tracking->getStatusCodeId()],
                'status_code_id' => $tracking->getStatusCodeId(),
                'carrier_desc' => $tracking->getCarrierDesc(),
                'user_id' => $tracking->getUserId(),
                'signatory' => $tracking->getSignatory(),
                'pod_image' => $trackingSignatoryImage,
                'parcel_image' => $trackingPracelImage,
                'tracking_country' => $trackingCountryName,
                'tracking_city' => $trackingCityName
            ];

            $trackingEvents[date('Y-m-d', strtotime($tracking->getDateCreated()))][] = $trackingEvent;
        }
        //$trackingDates = ['startDate' => $startDate, 'endDate' => $endDate];
        return $trackingEvents;
    }
}

// class
