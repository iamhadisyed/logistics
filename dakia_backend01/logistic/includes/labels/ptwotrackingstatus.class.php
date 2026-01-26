<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PtwoTrackingStatus
 *
 * @author kiran.iftikhar
 */
class PtwoTrackingStatus {
    
    public function __construct() {
        
    }
    
    public static $ptwo_status_code = array( 
        "10" => "Creating the shipment / shipment number.",
        "20" => "Exit customer",
        "30" => "Entrance in Hub",
        "40" => "Exit Hub",
        "50" => "The letter arrives at the mail service",
        "60" => "Exit Deliverer",
        "70" => "Delivered",
        "80" => "Letter Can not be delivered",
        "90" => "The letter comes back because it cannot be delivered",
        "100" => "Parcel can not be delivered",
        "110" => "Letter leaves the postal service",
        "120" => "Letter arrives at the hub",
        "130" => "Parcel can not be delivered",
        "140" => "Letter leaves Hub",
        "150" => "Returned",
        "200" => "Delivered"           
    );
 
    public static $consignment_status_code = array(
        "10" => Consignment::STATUS_LABEL_CREATED,
        "20" => Consignment::STATUS_DISPATCHED,
        "30" => Consignment::STATUS_INTRANSIT,
        "40" => Consignment::STATUS_INTRANSIT,
        "50" => Consignment::STATUS_INTRANSIT,
        "60" => Consignment::STATUS_INTRANSIT,
        "70" => Consignment::STATUS_DELIVERED,
        "80" => Consignment::STATUS_NOT_DELIVERED,
        "90" => Consignment::STATUS_RETURNED,
        "100" => Consignment::STATUS_NOT_DELIVERED,
        "110" => Consignment::STATUS_INTRANSIT,
        "120" => Consignment::STATUS_INTRANSIT,
        "130" => Consignment::STATUS_NOT_DELIVERED,
        "140" => Consignment::STATUS_INTRANSIT,
        "150" => Consignment::STATUS_RETURNED,
        "200" => Consignment::STATUS_DELIVERED,    
    );

    public static $oneworld_ptwo = array(        
        "10" => 144,
        "20" => 126,
        "30" => 137,
        "40" => 137,
        "50" => 146,
        "60" => 111,
        "70" => 121,
        "80" => 125,
        "90" => 138,
        "100" => 125,
        "110" => 137,
        "120" => 137,
        "130" => 125,
        "140" => 137,
        "150" => 138,
        "200" => 121         

    );
    public static function getOweStatusCode($ptwoStatus){
        $ptwoTrackingStatus = self::$oneworld_ptwo;
        return isset($ptwoTrackingStatus[$ptwoStatus]) ? $ptwoTrackingStatus[$ptwoStatus] : 0;
    }
    public static function getConsignmentStatus($ptwoStatus){
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$ptwoStatus]) ? $consignmentStatusCodes[$ptwoStatus] : 0;
    }
}
