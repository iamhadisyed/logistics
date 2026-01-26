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
class BelgiumPostTrackingStatus {
    
    public function __construct() {
        
    }
    
    public static $belgiumpost_status_code = array(
        "50" => "SHIPMENT INFORMATION RECEIVED",
        "60" => "SHIPMENT ALLOCATED",
        "75" => "SHIPMENT PROCESSED",
        "80" => "SHIPMENT FULFILLED",
        "90" => "SHIPMENT HELD FOR PAYMENT",
        "94" => "SHIPMENT PRECLEARANCE INFO RECEIVED",
        "95" => "SHIPMENT RECEIVED FOR PRECLEARANCE",
        "100" => "SHIPMENT INFORMATION TRANSMITTED TO CARRIER",
        "125" => "CUSTOMS_CLEARED",
        "130" => "CUSTOMS_VERIFIED",
        "135" => "CUSTOMS_ISSUE",
        "150" => "CROSSING_BORDER",
        "155" => "CROSSING_RECEIVED",
        "200" => "ITEM_SCANNED_AT_POSTAL_FACILITY",
        "225" => "ITEM_SCANNED_AT_FACILITY",
        "250" => "ITEM_SCANNED_FOR_CROSSING",
        "275" => "ITEM_TOUCHED_BY_CARRIER",
        "300" => "ITEM_OUT_FOR_DELIVERY",
        "400" => "ATTEMPTED_DELIVERY",
        "410" => "ITEM_AT_PICKUP_LOCATION",
        "450" => "ITEM_REDIRECTED_TO_NEW_ADDRESS",
        "500" => "ITEM_SUCCESSFULLY_DELIVERED",
        "550" => "RETURN_RECEIVED",
        "570" => "RETURN_FINALIZED",
        "800" => "CLAIM_ISSUED",
        "900" => "DELIVERY_REFUSED"
    );
 
    public static $consignment_status_code = array(
        "50" => Consignment::STATUS_LABEL_CREATED,
        "60" => Consignment::STATUS_RECEIVED,
        "75" => Consignment::STATUS_INTRANSIT,
        "80" => Consignment::STATUS_INTRANSIT,
        "90" => Consignment::STATUS_HOLD,
        "94" => Consignment::STATUS_LABEL_CREATED,
        "95" => Consignment::STATUS_INTRANSIT,
        "100" => Consignment::STATUS_RECEIVED,
        "125" => Consignment::STATUS_INTRANSIT,
        "130" => Consignment::STATUS_INTRANSIT,
        "135" => Consignment::STATUS_INTRANSIT,
        "150" => Consignment::STATUS_INTRANSIT,
        "155" => Consignment::STATUS_INTRANSIT,
        "200" => Consignment::STATUS_INTRANSIT,
        "225" => Consignment::STATUS_INTRANSIT,
        "250" => Consignment::STATUS_INTRANSIT,
        "275" => Consignment::STATUS_INTRANSIT,
        "300" => Consignment::STATUS_INTRANSIT,
        "400" => Consignment::STATUS_INTRANSIT,
        "410" => Consignment::STATUS_INTRANSIT,
        "450" => Consignment::STATUS_INTRANSIT,
        "500" => Consignment::STATUS_DELIVERED,
        "550" => Consignment::STATUS_RETURNED,
        "570" => Consignment::STATUS_RETURNED,
        "800" => Consignment::STATUS_AWATING_CLAIM,
        "900" => Consignment::STATUS_NOT_DELIVERED
    );

    public static $oneworld_belgiumpost = array(
        "50" => 144,
        "60" => 137,
        "75" => 137,
        "80" => 137,
        "90" => 128,
        "94" => 144,
        "95" => 137,
        "100" => 137,
        "125" => 117,
        "130" => 117,
        "135" => 117,
        "150" => 137,
        "155" => 137,
        "200" => 137,
        "225" => 137,
        "250" => 137,
        "275" => 137,
        "300" => 111,
        "400" => 123,
        "410" => 137,
        "450" => 137,
        "500" => 121,
        "550" => 138,
        "570" => 138,
        "800" => 155,
        "900" => 125
    );
    public static function getOweStatusCode($belgiumpostStatus){
        $belgiumpostTrackingStatus = self::$oneworld_belgiumpost;
        return isset($belgiumpostTrackingStatus[$belgiumpostStatus]) ? $belgiumpostTrackingStatus[$belgiumpostStatus] : 0;
    }
    public static function getConsignmentStatus($belgiumpostStatus){
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$belgiumpostStatus]) ? $consignmentStatusCodes[$belgiumpostStatus] : 0;
    }
}
