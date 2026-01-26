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
class DhlTrackingStatus {
    
    public function __construct() {
        
    }
    
    public static $dhl_status_code = array(
 
        'BA' => "Bad Address",
        'CA' => "Closed on Arrival",
        'CD' => "Clearance Delay",
        'CM' => "Consignee Moved",
        'HP' => "Held for Payment",
        'IA' => "Image Available" ,
        'MC' => "Miscode",
        'MD' => "Missed Delivery Cycle",
        'MS' => "Missort",
        'NA' => "Not Arrived",
        'ND' => "Not Delivered",
        'NH' => "Not Home",
        'OH' => "On Hold",
        'RD' => "Refused Delivery",
        'SC' => "Service Changed",
        'TD' => "Transport Delay",
        'UD' => "Uncontrollable Clearance Delay",

        'BR' => "Cleared and Delivered by Broker",
        'CS' => "Closed Shipments",
        'DD' => "Delivered Damaged",
        'DM' => "Damaged",
        'DS' => "Destroyed/Disposal",
        'OK' => "Delivery",
        'RT' => "Retuned to Consignor",
        'SS' => "Shipment Stopped",
        'TP' => "Forwarded to Third party",

        'AD' => "Agreed Delivery",
        'AF' => "Arrived Facility",
        'AR' => "Arrival at delivery facility",
        'BL' => "Bond Location",
        'BN' => "Broker Notified",
        'CC' => "Awaiting Consignee Collection",
        'CI' => "Facility CheckIn",
        'CR' => "Clearance Release",
        'CU' => "Confirm Uplift",
        'DF' => "Depart Facility",
        'ES' => "Entry Submitted",
        'FD' => "Forwarded to Third Party Delivery Agent",
        'HI' => "Lodged into Held Inventory Control",
        'HO' => "Lodged out of Held Inventory Control",
        'IC' => "In Clearance Processing",
        'PD' => "Partial Delivery",
        'PL' => "Processed at Location",
        'PO' => "Processed at Origin",
        'PU' => "Shipment Pickup",
        'RR' => "Customs status updated",
        'RW' => "Weight and Dimension",
        'SA' => "Shipment Acknowledged",
        'SD' => "Shipment Detail",
        'SI' => "Security Inspection",
        'SM' => "Scheduled for Movement",
        'ST' => "Shipment Intercept",
        'TI' => "Trace Initiated",
        'TR' => "Record of Transit",
        'TT' => "Trace Terminated",
        'WC' => "With Delivering Courier",
        'YY' => "Passed Warehouse without scan"
       
    );
 
    public static $consignment_status_code = array(
        'BA' => Consignment::STATUS_PROBLEM,
        'CA' => Consignment::STATUS_INTRANSIT,
        'CD' => Consignment::STATUS_INTRANSIT,
        'CM' => Consignment::STATUS_PROBLEM,
        'HP' => Consignment::STATUS_HOLD,
        'IA' => Consignment::STATUS_INTRANSIT,
        'MC' => Consignment::STATUS_PROBLEM,
        'MD' => Consignment::STATUS_PROBLEM,
        'MS' => Consignment::STATUS_PROBLEM,
        'NA' => Consignment::STATUS_PROBLEM,
        'ND' => Consignment::STATUS_PROBLEM,
        'NH' => Consignment::STATUS_PROBLEM,
        'OH' => Consignment::STATUS_HOLD,
        'RD' => Consignment::STATUS_DISCREPANCY,
        'SC' => Consignment::STATUS_PROBLEM,
        'TD' => Consignment::STATUS_PROBLEM,
        'UD' => Consignment::STATUS_PROBLEM,

        'BR' => Consignment::STATUS_DELIVERED,
        'CS' => Consignment::STATUS_CLOSE,
        'DD' => Consignment::STATUS_DISCREPANCY,
        'DM' => Consignment::STATUS_DISCREPANCY,
        'DS' => Consignment::STATUS_DISCREPANCY,
        'OK' => Consignment::STATUS_DELIVERED,
        'RT' => Consignment::STATUS_RETURNED,
        'SS' => Consignment::STATUS_HOLD,
        'TP' => Consignment::STATUS_INTRANSIT,

        'AD' => Consignment::STATUS_INTRANSIT,
        'AF' => Consignment::STATUS_INTRANSIT,
        'AR' => Consignment::STATUS_INTRANSIT,
        'BL' => Consignment::STATUS_INTRANSIT,
        'BN' => Consignment::STATUS_INTRANSIT,
        'CC' => Consignment::STATUS_HOLD,
        'CI' => Consignment::STATUS_INTRANSIT,
        'CR' => Consignment::STATUS_INTRANSIT,
        'CU' => Consignment::STATUS_INTRANSIT,
        'DF' => Consignment::STATUS_INTRANSIT,
        'ES' => Consignment::STATUS_INTRANSIT,
        'FD' => Consignment::STATUS_INTRANSIT,
        'HI' => Consignment::STATUS_HOLD,
        'HO' => Consignment::STATUS_HOLD,
        'IC' => Consignment::STATUS_INTRANSIT,
        'PD' => Consignment::STATUS_PARTIAL_DELIVERED,
        'PL' => Consignment::STATUS_INTRANSIT,
        'PO' => Consignment::STATUS_INTRANSIT,
        'PU' => Consignment::STATUS_INTRANSIT,
        'RR' => Consignment::STATUS_INTRANSIT,
        'RW' => Consignment::STATUS_INTRANSIT,
        'SA' => Consignment::STATUS_INTRANSIT,
        'SD' => Consignment::STATUS_INTRANSIT,
        'SI' => Consignment::STATUS_INTRANSIT,
        'SM' => Consignment::STATUS_INTRANSIT,
        'ST' => Consignment::STATUS_INTRANSIT,
        'TI' => Consignment::STATUS_INTRANSIT,
        'TR' => Consignment::STATUS_INTRANSIT,
        'TT' => Consignment::STATUS_INTRANSIT,
        'WC' => Consignment::STATUS_INTRANSIT,
        'YY' => Consignment::STATUS_INTRANSIT
    );

    public static $oneworld_dhl = array(
        'BA' => 112,
        'CA' => 116,
        'CD' => 120,
        'CM' => 116,
        'HP' => 128,
        'IA' => 137,
        'MC' => 115,
        'MD' => 120,
        'MS' => 115,
        'NA' => 137,
        'ND' => 124,
        'NH' => 123,
        'OH' => 128,
        'RD' => 111,
        'SC' => 136,
        'TD' => 137,
        'UD' => 117,

        'BR' => 117,
        'CS' => 127,
        'DD' => 118,
        'DM' => 119,
        'DS' => 119,
        'OK' => 121,
        'RT' => 138,
        'SS' => 127,
        'TP' => 137,

        'AD' => 123,
        'AF' => 146,
        'AR' => 146,
        'BL' => 137,
        'BN' => 137,
        'CC' => 144,
        'CI' => 114,
        'CR' => 117,
        'CU' => 137,
        'DF' => 145,
        'ES' => 137,
        'FD' => 137,
        'HI' => 128,
        'HO' => 137,
        'IC' => 117,
        'PD' => 147,
        'PL' => 137,
        'PO' => 137,
        'PU' => 133,
        'RR' => 137,
        'RW' => 141,
        'SA' => 137,
        'SD' => 137,
        'SI' => 137,
        'SM' => 137,
        'ST' => 137,
        'TI' => 137,
        'TR' => 137,
        'TT' => 137,
        'WC' => 137,
        'YY' =>137

    );
    public static function getOweStatusCode($dhlStatus){
        $dhlTrackingStatus = self::$oneworld_dhl;
        return isset($dhlTrackingStatus[$dhlStatus]) ? $dhlTrackingStatus[$dhlStatus] : 0;
    }
    public static function getConsignmentStatus($dhlStatus){
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$dhlStatus]) ? $consignmentStatusCodes[$dhlStatus] : 0;
    }
}
