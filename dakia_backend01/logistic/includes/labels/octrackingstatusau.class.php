<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of YodelTrackingStatus
 *
 * @author kiran.iftikhar
 */
class OcTrackingStatusAu {
    
    public function __construct() {
        
    }
    
    public static $oc_status_code_au = array(
        "MANIFEST" =>  "Carrier receives the request of the shipment, but the shipment is not physically with the carrier",
        "PICKUP_DROPOFF_REJECTED" => "Carrier reject to pick up the package from the sender for any reason",
        "FIRST_SCAN" => "parcel has physically obtained and acceptance scan is done",
        "IN_TRANSIT" => "In transit",
        "DELIVERED" => "Delivered",
        "OUT_FOR_DELIVERY" => "The package is out for delivery to the final destination",
        "DELIVERY_ATTEMPT" => "The carrier attempted to deliver the package but was unsuccessful",
        "RETURN_INITIATED" => "Parcel return to seller process is initiated",
        "RETURNED_TO_SENDER" => "Parcel is successfully returned to the sender",
        "PACKAGE_TERMINATED" => "During return, the parcel was completely destroyed",
        "READY_FOR_PICKUP" => "The parcel is in the destination city and ready for the recipient to come and pick up.",
        "PICKED_UP" => "The parcel is picked up by the recipient", 
        "PICKUP_MISSED" => "The recipient cannot pick up the parcel",
        "ICC" => "Import Customs Clearance Completed",
        "ERROR" => "Package Held by Import Customs ",
        "DELIVERY_ATTEMPT" => "Business not open. Post office attempted delivery. Notice left",
        "DELIVERY_EXCEPTION" => "Returned to Destination Facility",
    );
 
    public static $consignment_status_code_au = array(
        "MANIFEST" =>  Consignment::STATUS_LABEL_CREATED,
        "PICKUP_DROPOFF_REJECTED" => Consignment::STATUS_PROBLEM,
        "FIRST_SCAN" => Consignment::STATUS_RECEIVED,
        "IN_TRANSIT" => Consignment::STATUS_INTRANSIT,
        "DELIVERED" => Consignment::STATUS_DELIVERED,
        "OUT_FOR_DELIVERY" => Consignment::STATUS_INTRANSIT,
        "DELIVERY_ATTEMPT" => Consignment::STATUS_INTRANSIT,
        "DELIVERY_EXCEPTION" => Consignment::STATUS_PROBLEM,
        "RETURN_INITIATED" => Consignment::STATUS_RETURNED,
        "RETURNED_TO_SENDER" => Consignment::STATUS_RETURNED,
        "PACKAGE_TERMINATED" => Consignment::STATUS_RECYCLED,
        "READY_FOR_PICKUP" => Consignment::STATUS_INTRANSIT,
        "PICKED_UP" => Consignment::STATUS_DELIVERED,
        "PICKUP_MISSED" => Consignment::STATUS_PROBLEM,
        "ICC" => Consignment::STATUS_INTRANSIT,
        "ERROR" => Consignment::STATUS_PROBLEM,
        "DELIVERY_ATTEMPT" => Consignment::STATUS_INTRANSIT,
        "DELIVERY_EXCEPTION" => Consignment::STATUS_RETURNED
    );

    public static $oneworld_oc_au = array(
        "MANIFEST" =>  144,
        "PICKUP_DROPOFF_REJECTED" => 131,
        "FIRST_SCAN" => 148,
        "IN_TRANSIT" => 137,
        "DELIVERED" => 121,
        "OUT_FOR_DELIVERY" => 111,
        "DELIVERY_ATTEMPT" => 123,
        "DELIVERY_EXCEPTION" => 120,
        "RETURN_INITIATED" => 138,
        "RETURNED_TO_SENDER" => 138,
        "PACKAGE_TERMINATED" => 119,
        "READY_FOR_PICKUP" => 137,
        "PICKED_UP" => 133,
        "PICKUP_MISSED" => 131,
        "ERROR" => 128,
        "ICC" => 117,
        "DELIVERY_ATTEMPT" => 123,
        "DELIVERY_EXCEPTION" => 138
        
    );
    public static function getOweStatusCode($ocStatusAu){
        $ocTrackingStatusAu = self::$oneworld_oc_au;
        return isset($ocTrackingStatusAu[$ocStatusAu]) ? $ocTrackingStatusAu[$ocStatusAu] : 0;
    }
    public static function getConsignmentStatus($ocStatusAu){
        $consignmentStatusCodesAu = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodesAu[$ocStatusAu]) ? $consignmentStatusCodesAu[$ocStatusAu] : 0;
    }
}
