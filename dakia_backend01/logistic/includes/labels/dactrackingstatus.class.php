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
class DacTrackingStatus {
    
    public function __construct() {
        
    }
    
    public static $dac_status_code = array( 
           "00" => "Check in @ D.A.C. main warehouse",
           "01" => "Missing client at first passage",
           "02" => "Delivered",
           "03" => "Addressee moved",
           "04" => "Refused by addressee",
           "05" => "Addressee haven't money",
           "06" => "Close for holyday",
           "07" => "Missing client at second passage",
           "08" => "Close for day off",
           "10" => "Warehouse pick up hasn't picked up",
           "12" => "Warehouse pick up requested",
           "13" => "Error in linehaul",
           "14" => "Delivery booked",
           "15" => "Over dimension",
           "16" => "Moving to other warehouse",
           "17" => "Linehaul delay",
           "19" => "Distribution problem",
           "22" => "48 h delivery location",
           "23" => "72 h delivery location",
           "24" => "Damaged parcel",
           "25" => "Incomplete shipment",
           "26" => "Incomplete shipment, 2nd warning",
           "27" => "Incomplete shipment, waiting for completing",
           "28" => "Waiting at warehouse",
           "29" => "Items back to sender",
           "30" => "Theft",
           "34" => "Delivery in small island",
           "35" => "Given to partner company",
           "39" => "Destroyed as requested",
           "49" => "Missing documents",
           "59" => "Company closed",
           "66" => "Waiting instructions",
           "67" => "Company closed for holiday",
           "68" => "Wrong address",               
           "69" => "Unavailable addressee",
           "70" => "Return refused",
           "71" => "Return document missing",
           "73" => "Addressee haven't money, 2nd time",
           "74" => "Delivery reserved",
           "75" => "Waiting instructions",
           "80" => "Out for delivery",
           "81" => "Check out from D.A.C. main warehouse",
           "82" => "Information sent to D.A.C. Italy"
    );
 
    public static $consignment_status_code = array(
       
           "00" => Consignment::STATUS_INTRANSIT,
           "01" => Consignment::STATUS_PROBLEM,
           "02" => Consignment::STATUS_DELIVERED,
           "03" => Consignment::STATUS_PROBLEM,
           "04" => Consignment::STATUS_PROBLEM,
           "05" => Consignment::STATUS_PROBLEM,
           "06" => Consignment::STATUS_INTRANSIT,
           "07" => Consignment::STATUS_PROBLEM,
           "08" => Consignment::STATUS_INTRANSIT,
           "10" => Consignment::STATUS_PROBLEM,
           "12" => Consignment::STATUS_INTRANSIT,
           "13" => Consignment::STATUS_PROBLEM,
           "14" => Consignment::STATUS_INTRANSIT,
           "15" => Consignment::STATUS_PROBLEM,
           "16" => Consignment::STATUS_INTRANSIT,
           "17" => Consignment::STATUS_PROBLEM,
           "19" => Consignment::STATUS_PROBLEM,
           "22" => Consignment::STATUS_INTRANSIT,
           "23" => Consignment::STATUS_INTRANSIT,
           "24" => Consignment::STATUS_PROBLEM,
           "25" => Consignment::STATUS_PROBLEM,
           "26" => Consignment::STATUS_PROBLEM,
           "27" => Consignment::STATUS_PROBLEM,
           "28" => Consignment::STATUS_INTRANSIT,
           "29" => Consignment::STATUS_RETURNED,
           "30" => Consignment::STATUS_PROBLEM,
           "34" => Consignment::STATUS_DELIVERED,
           "35" => Consignment::STATUS_INTRANSIT,
           "39" => Consignment::STATUS_INTRANSIT,
           "49" => Consignment::STATUS_PROBLEM,
           "59" => Consignment::STATUS_PROBLEM,
           "66" => Consignment::STATUS_INTRANSIT,
           "67" => Consignment::STATUS_PROBLEM,
           "68" => Consignment::STATUS_PROBLEM,               
           "69" => Consignment::STATUS_PROBLEM,
           "70" => Consignment::STATUS_PROBLEM,
           "71" => Consignment::STATUS_PROBLEM,
           "73" => Consignment::STATUS_PROBLEM,
           "74" => Consignment::STATUS_INTRANSIT,
           "75" => Consignment::STATUS_INTRANSIT,
           "80" => Consignment::STATUS_INTRANSIT,
           "81" => Consignment::STATUS_INTRANSIT,
           "82" => Consignment::STATUS_INTRANSIT,
    );

    public static $oneworld_dac = array(
        
           "00" => 137,
           "01" => 115,
           "02" => 121,
           "03" => 112,
           "04" => 115,
           "05" => 115,
           "06" => 115,
           "07" => 115,
           "08" => 137,
           "10" => 115,
           "12" => 137,
           "13" => 115,
           "14" => 137,
           "15" => 115,
           "16" => 137,
           "17" => 115,
           "19" => 115,
           "22" => 137,
           "23" => 137,
           "24" => 119,
           "25" => 115,
           "26" => 115,
           "27" => 115,
           "28" => 137,
           "29" => 138,
           "30" => 115,
           "34" => 121,
           "35" => 137,
           "39" => 137,
           "49" => 115,
           "59" => 115,
           "66" => 137,
           "67" => 137,
           "68" => 112,
           "69" => 112,
           "70" => 115,
           "71" => 115,
           "73" => 115,
           "74" => 137,
           "75" => 137,
           "80" => 111,
           "81" => 137,
           "82" => 137,
    );
    public static function getOweStatusCode($dacStatus){
        $dacTrackingStatus = self::$oneworld_dac;
        return isset($dacTrackingStatus[$dacStatus]) ? $dacTrackingStatus[$dacStatus] : 0;
    }
    public static function getConsignmentStatus($dacStatus){
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$dacStatus]) ? $consignmentStatusCodes[$dacStatus] : 0;
    }
}
