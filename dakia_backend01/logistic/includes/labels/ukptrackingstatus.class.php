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
class UkpTrackingStatus {
    
    public function __construct() {
        
    }
    
    public static $ukp_status_code = array(
        "X1"  =>  "Processed at UKP",
        "X2"  =>  "Handed over to Airline",
        "X3"  =>  "Departed UK",
        "81"  =>  "Arrived Shipping Partner Facility, USPS Awaiting Item",
        "82"  =>  "Departed Shipping Partner Facility, USPS Awaiting Item",
        "10"  =>  "Arrived at USPS Regional Origin Facility",
        "NT"  =>  "In Transit",
        "27"  =>  "Unclaimed/Being Returned to Sender",
        "22"  =>  "Insufficient Address",
        "10"  =>  "Arrived at USPS Regional Destination Facility",
        "07"  =>  "Arrived at Post Office",
        "07"  =>  "Arrived at Hub",
        "OF"  =>  "Out for Delivery",
        "01"  =>  "Delivered",
        "01"  =>  "Delivered, Front Door/Porch",
        "01"  =>  "Delivered, In/At Mailbox",
        "1"   =>   "Delivered, Front Door/Porch",
        "1"   =>   "Delivered, In/At Mailbox",
        "1"   =>  "Delivered",
        "A1"  =>  "Arrived at USPS Facility",
        "01"   =>  "Delivered, Front Desk/Reception/Mail Room",
        "29" => "Return to Sender Processed"
    );
 
    public static $consignment_status_code = array(
        "X1"  =>  Consignment::STATUS_INTRANSIT,
        "X2"  =>  Consignment::STATUS_INTRANSIT,
        "X3"  =>  Consignment::STATUS_INTRANSIT,
        "81"  =>  Consignment::STATUS_INTRANSIT,
        "82"  =>  Consignment::STATUS_INTRANSIT,
        "10"  =>  Consignment::STATUS_INTRANSIT,
        "NT"  =>  Consignment::STATUS_INTRANSIT,
        "27"  =>  Consignment::STATUS_RETURNED,
        "22"  =>  Consignment::STATUS_PROBLEM,
        "10"  =>  Consignment::STATUS_INTRANSIT,
        "07"  =>  Consignment::STATUS_INTRANSIT,
        "07"  =>  Consignment::STATUS_INTRANSIT,
        "OF"  =>  Consignment::STATUS_INTRANSIT,
        "01"  =>  Consignment::STATUS_DELIVERED,       
        "1"   =>  Consignment::STATUS_DELIVERED,
        "A1"  =>  Consignment::STATUS_INTRANSIT,
        "29" => Consignment::STATUS_RETURNED
    );
//need to map again oneworld status 
    public static $oneworld_ukp = array(
        "X1"  =>  148,
        "X2"  =>  137,
        "X3"  =>  137,
        "81"  =>  137,
        "82"  =>  137,
        "10"  =>  137,
        "NT"  =>  137,
        "27"  =>  143,
        "22"  =>  112,
        "10"  =>  137,
        "07"  =>  137,
        "07"  =>  137,
        "OF"  =>  111,
        "01"  =>  121,
        "01"  =>  121,
        "01"  =>  121,
        "1"   =>  121,
        "A1"  =>  137,
        "29" => 138
        );
    public static function getOweStatusCode($ukpStatus){
        $ukpTrackingStatus = self::$oneworld_ukp;
        return isset($ukpTrackingStatus[$ukpStatus]) ? $ukpTrackingStatus[$ukpStatus] : 0;        
    }
    public static function getConsignmentStatus($ukpStatus){
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$ukpStatus]) ? $consignmentStatusCodes[$ukpStatus] : 0;
    }
}
