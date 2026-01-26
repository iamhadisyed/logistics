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
class KaabTrackingStatus {
    
    public function __construct() {
        
    }
    
    public static $kaab_status_code = array( 
           "1" => "New Order",
           "2" => "Shipment Injected",
           "10" => "Received in HUB",
           "20" => "In Transportation",
           "30" => "In Delivery",
           "40" => "Delivered",
           "50" => "Problem",
           "60" => "Return",
           "90" => "Cancelled",
          
    );
 
    public static $consignment_status_code = array(
       
            "1" => Consignment::STATUS_NEW,
            "2" => Consignment::STATUS_LABEL_CREATED,
           "10" => Consignment::STATUS_INTRANSIT,
           "20" => Consignment::STATUS_INTRANSIT,
           "30" => Consignment::STATUS_INTRANSIT,
           "40" => Consignment::STATUS_DELIVERED,
           "50" => Consignment::STATUS_PROBLEM,
           "60" => Consignment::STATUS_RETURNED,
           "90" => Consignment::STATUS_CLOSE,
           
    );

    public static $oneworld_kaab = array(        
           "1" => 144,
           "2" => 137,
           "10" => 137,
           "20" => 137,
           "30" => 111,
           "40" => 121,
           "50" => 115,
           "60" => 138,
           "90" => 136,
           
    );
    public static function getOweStatusCode($kaabStatus){
        $kaabTrackingStatus = self::$oneworld_kaab;
        return isset($kaabTrackingStatus[$kaabStatus]) ? $kaabTrackingStatus[$kaabStatus] : 0;
    }
    public static function getConsignmentStatus($kaabStatus){
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$kaabStatus]) ? $consignmentStatusCodes[$kaabStatus] : 0;
    }
}
