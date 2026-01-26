<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ParcelforYouTrackingStatus
 *
 * @author kiran.iftikhar
 */

class PitneyBowsTrackingStatus {

    public function __construct() {        
    }
    
    public static $pitneybows_status_code = array(
        "CUR" => "Customs Receipt",
        "CUC" => "Customs Cleared",
        "DLD" => "Delivered",
        "LPF" => "Departed from shipping partner facility",
        "HFI" => "Held for Inspection",
        "RFI" => "Released from Inspection",
        "DCC" => "Delay in Custom Clearance",
        "ERC" => "Exception - Rejected by Customs",
        "TRC" => "Shipment in transit to carrier",
        "TRP" => "In transit to PB",
        "PCB" => "Picked up from Customs Broker",
        "DPB" => "Dropped off at PB facility"
    );
    
    public static $consignment_status_code = array(
        "CUR" => Consignment::STATUS_INTRANSIT,
        "CUC" => Consignment::STATUS_INTRANSIT,
        "DLD" => Consignment::STATUS_DELIVERED,
        "LPF" => Consignment::STATUS_INTRANSIT,
        "HFI" => Consignment::STATUS_INTRANSIT,
        "RFI" => Consignment::STATUS_INTRANSIT,
        "DCC" => Consignment::STATUS_INTRANSIT,
        "ERC" => Consignment::STATUS_PROBLEM,
        "TRC" => Consignment::STATUS_INTRANSIT,
        "TRP" => Consignment::STATUS_INTRANSIT,
        "PCB" => Consignment::STATUS_INTRANSIT,
        "DPB" => Consignment::STATUS_INTRANSIT
    );
    
    public static $oneworld_pitneybows = array(
        "CUR" => 137,
        "CUC" => 117,
        "DLD" => 121,
        "LPF" => 126,
        "HFI" => 128,
        "RFI" => 137,
        "DCC" => 120,
        "ERC" => 115,
        "TRC" => 137,
        "TRP" => 137,
        "PCB" => 133,
        "DPB" => 159
    );

    public static function getOweStatusCode($pitneybowsStatus) {
        $pitneybowsTrackingStatus = self::$oneworld_pitneybows;
        return isset($pitneybowsTrackingStatus[$pitneybowsStatus]) ? $pitneybowsTrackingStatus[$pitneybowsStatus] : 0;
    }

    public static function getConsignmentStatus($pitneybowsStatus) {
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$pitneybowsStatus]) ? $consignmentStatusCodes[$pitneybowsStatus] : 0;
    }

}
