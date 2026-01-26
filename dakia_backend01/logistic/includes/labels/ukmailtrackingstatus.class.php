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
class UkMailTrackingStatus {
    
    public function __construct() {
        
    }
    
    public static $ukmail_status_code = array(
        "1"  =>  "Awaiting Collection",
        "2"  =>  "Collected",
        "3"  =>  "At Delivery Location",
        "4"  =>  "Out For Delivery",
        "5"  =>  "Delivered",	
        "6"  =>  "Part Delivered",
        "7"  =>  "Delivery Attempted",
        "8"  =>  "Delayed",		
        "9"  =>  "Please Call",	
        "10" =>  "Awaiting Delivery",
        "DT01" => "Signed for by consignee",
        "DT02" => "Secure Location � In the Porch",
        "DT03" => "Secure Location � Behind The Gate",
        "DT04" => "Secure Location � In the shed",
        "DT05" => "Secure Location � In the garage",	
        "DT06" => "Secure Location � With porter/caretaker",
        "DT07" => "Secure Location � In conservatory",
        "DT08" => "Secure Location � In greenhouse",
        "DT09" => "Other Secure Location",
        "DT10" => "Left with neighbour",
        "DT11" => "Recipient Specified Delivery" 		
    );
 
    public static $consignment_status_code = array(
        'BA'   =>  Consignment::STATUS_PROBLEM,
        "1"    =>  Consignment::STATUS_INTRANSIT,
        "2"    =>  Consignment::STATUS_INTRANSIT,
        "3"    =>  Consignment::STATUS_INTRANSIT,
        "4"    =>  Consignment::STATUS_INTRANSIT,
        "5"    =>  Consignment::STATUS_DELIVERED,
        "6"    =>  Consignment::STATUS_PARTIAL_DELIVERED,
        "7"    =>  Consignment::STATUS_INTRANSIT,
        "8"    =>  Consignment::STATUS_INTRANSIT,		
        "9"    =>  Consignment::STATUS_PROBLEM,
        "10"   =>  Consignment::STATUS_INTRANSIT,		
        "DT01" =>  Consignment::STATUS_INTRANSIT,		
        "DT02" =>  Consignment::STATUS_DELIVERED,
        "DT03" =>  Consignment::STATUS_DELIVERED,
        "DT04" =>  Consignment::STATUS_DELIVERED,
        "DT05" =>  Consignment::STATUS_DELIVERED,
        "DT06" =>  Consignment::STATUS_DELIVERED,
        "DT07" =>  Consignment::STATUS_DELIVERED,
        "DT08" =>  Consignment::STATUS_DELIVERED,
        "DT09" =>  Consignment::STATUS_DELIVERED,
        "DT10" =>  Consignment::STATUS_DELIVERED,
        "DT11" =>  Consignment::STATUS_DELIVERED,
    );

    public static $oneworld_ukmail = array(
        "1"  =>  144,
        "2"  =>  137,
        "3"  =>  137,
        "4"  =>  111,
        "5"  =>  121,
        "6"  =>  147,
        "7"  =>  123,
        "8"  =>  120,
        "9"  =>  136,
        "10" =>  137,
        "DT01" => 121,
        "DT02" => 121,
        "DT03" => 121,
        "DT04" => 121,
        "DT05" => 121,
        "DT06" => 121,
        "DT07" => 121,
        "DT08" => 121,
        "DT09" => 121,
        "DT10" => 121,
        "DT11" => 121
    );
    public static function getOweStatusCode($ukmailStatus){
        $ukmailTrackingStatus = self::$oneworld_ukmail;
        return isset($ukmailTrackingStatus[$ukmailStatus]) ? $ukmailTrackingStatus[$ukmailStatus] : 0;
    }
    public static function getConsignmentStatus($ukmailStatus){
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$ukmailStatus]) ? $consignmentStatusCodes[$ukmailStatus] : 0;
    }
}
