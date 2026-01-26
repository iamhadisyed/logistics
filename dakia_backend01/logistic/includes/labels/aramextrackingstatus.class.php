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
class AramexTrackingStatus {
    
    public function __construct() {
        
    }
    
    public static $aramex_status_code = array(
        "SH022"  =>  "Departed Operations facility – In Transit",
        "SH005"  =>  "Delivered",
        "SH003"  =>  "Out for Delivery",
        "SH382"  =>  "Shipment Update",	
        "SH012"  =>  "Picked Up From Shipper",
        "SH160"  =>  "Under processing at operations facility",
        "SH001"  =>  "Under processing at operations facility",		
        "SH014"  =>  "Record created",
        "SH047"  =>  "Received at Origin Facility",
        "SH006"  =>  "Collected by Consignee",
        "SH041"  =>  "Cleared from Customs",
        "SH156"  =>  "Customs Clearance - In Progress"
        
    );
 
    public static $consignment_status_code = array(
        "SH022"  =>  Consignment::STATUS_INTRANSIT,
        "SH005"  =>  Consignment::STATUS_DELIVERED,
        "SH003"  =>  Consignment::STATUS_INTRANSIT,
        "SH382"  =>  Consignment::STATUS_INTRANSIT,
        "SH012"  =>  Consignment::STATUS_INTRANSIT,
        "SH160"  =>  Consignment::STATUS_INTRANSIT,
        "SH001"  =>  Consignment::STATUS_INTRANSIT,		
        "SH014"  =>  Consignment::STATUS_LABEL_CREATED,
        "SH047"  =>  Consignment::STATUS_INTRANSIT,
        "SH006"  =>  Consignment::STATUS_INTRANSIT,
        "SH041"  =>  Consignment::STATUS_INTRANSIT,
        "SH156"  =>  Consignment::STATUS_INTRANSIT,
    );

    public static $oneworld_aramex = array(
        "SH022"  =>  137,
        "SH005"  =>  121,
        "SH003"  =>  111,
        "SH382"  =>  137,
        "SH012"  =>  133,
        "SH160"  =>  137,
        "SH001"  =>  137,
        "SH014"  =>  144,
        "SH047"  =>  148,
        "SH006"  =>  140,
        "SH041"  =>  117,
        "SH156"  =>  117
    );
    public static function getOweStatusCode($aramexStatus){
        $aramexTrackingStatus = self::$oneworld_aramex;
        return isset($aramexTrackingStatus[$aramexStatus]) ? $aramexTrackingStatus[$aramexStatus] : 0;
    }
    public static function getConsignmentStatus($aramexStatus){
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$aramexStatus]) ? $consignmentStatusCodes[$aramexStatus] : 0;
    }
}
