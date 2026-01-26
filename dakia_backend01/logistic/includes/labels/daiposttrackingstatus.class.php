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
class DaiPostTrackingStatus {
    
    public function __construct() {
        
    }
    
    public static $daipost_status_code = array( 
        'CI02' => 'Customer Information Manifested',
        'CI03' => 'Shipment cancelled by Customer',
        'DA01' => 'Shipment Arrived within Destination Country',
        'DC01' => 'Cleared Customs',
        'DC02' => 'Customs Released',
        'DC11' => 'Customs Clearance Delay',
        'DC25' => 'Customs Inspection Pending',
        'DC29' => 'DAFF Inspection Pending',
        'DC30' => 'Cargo Siezed by Customs',
        'DE03' => 'Parcel Damaged',
        'DE10' => 'Parcel Disposed',
        'DE20' => 'Return to Sender - Re-export',
        'DH01' => 'Handed Over to Partner Carrier',
        'FD01' => 'Parcel Delivered',
        'FD02' => 'Parcel Delivered with Safe Drop',
        'FD03' => 'Parcel left with your neighbour',
        'FD10' => 'Attempted Delivery',
        'FD11' => 'Attempted Delivery - No One to Receive',
        'FD21' => 'Parcel Available for Pick Up',
        'FD22' => 'Uncollected by Customer',
        'FD31' => 'Parcel Delivered',
        'FE01' => 'Undeliverable - Parcel Returned',
        'FE03' => 'Undeliverable - Incorrect Address',
        'FE04' => 'Undeliverable - Refused by Customer',
        'FE07' => 'Under Investigation by Carrier',
        'FE10' => 'Return to Warehouse',
        'FE11' => 'Return to Warehouse',
        'FI01' => 'Parcel Data Received',
        'FP01' => 'Parcel Processed at Carrier Terminal',
        'FP03' => 'Parcel In-Transit',
        'FP05' => 'Out For Delivery',
        'FP07' => 'Parcel received by Carrier',
        'OP01' => 'Parcel Processed at Origin Hub',
        'OP02' => 'Manifested for Outbound Transportation',
        'OP03' => 'Batch Processed at Origin Hub',
        'OU01' => 'Shipment Departed Origin Country',
        'WE01' => 'Deemed Lost in Transit',
        'WR01' => 'Delivered to Returns Centre',
        'WR10' => 'Received at Braeside (Vic) warehouse',
        'WR11' => 'Returned to DAI - Insufficient Address',
        'WR11' => 'Returned to DAI - Left Address/Unknown at Address',
        'WR12' => 'Returned to DAI - Refused',
        'WR13' => 'Returned to DAI - Unclaimed',
        'WR14' => 'Returned to DAI - RTS',
        'WR15' => 'Returned to DAI - Incorrect Address',
        'WR16' => 'Returned to DAI - Unknown Reason',
        'WR17' => 'Returned to DAI - Consumer Return',
        'WR21' => 'Re-shipped from Braeside (Vic) Warehouse'
    );
 
    public static $consignment_status_code = array(
       
            'CI02' => Consignment::STATUS_DISPATCHED,
            'DA01' => Consignment::STATUS_INTRANSIT,
            'DC01' => Consignment::STATUS_INTRANSIT,
            'DC02' => Consignment::STATUS_INTRANSIT,
            'DC11' => Consignment::STATUS_INTRANSIT,
            'DC25' => Consignment::STATUS_INTRANSIT,
            'DC29' => Consignment::STATUS_INTRANSIT,
            'DC30' => Consignment::STATUS_PROBLEM,
            'DE03' => Consignment::STATUS_PROBLEM,
            'DE10' => Consignment::STATUS_PROBLEM,
            'DE20' => Consignment::STATUS_RETURNED,
            'DH01' => Consignment::STATUS_PARTIAL_DELIVERED,
            'FD01' => Consignment::STATUS_DELIVERED,
            'FD02' => Consignment::STATUS_DELIVERED,
            'FD03' => Consignment::STATUS_DELIVERED,
            'FD10' => Consignment::STATUS_INTRANSIT,
            'FD11' => Consignment::STATUS_INTRANSIT,
            'FD21' => Consignment::STATUS_INTRANSIT,
            'FD22' => Consignment::STATUS_INTRANSIT,
            'FD31' => Consignment::STATUS_DELIVERED,
            'FE01' => Consignment::STATUS_RETURNED,
            'FE03' => Consignment::STATUS_PROBLEM,
            'FE04' => Consignment::STATUS_PROBLEM,
            'FE07' => Consignment::STATUS_PROBLEM,
            'FE10' => Consignment::STATUS_RETURNED,
            'FE11' => Consignment::STATUS_RETURNED,
            'FI01' => Consignment::STATUS_RECEIVED,
            'FP01' => Consignment::STATUS_INTRANSIT,
            'FP03' => Consignment::STATUS_INTRANSIT,
            'FP05' => Consignment::STATUS_INTRANSIT,
            'FP07' => Consignment::STATUS_INTRANSIT,
            'OP01' => Consignment::STATUS_INTRANSIT,
            'OP02' => Consignment::STATUS_INTRANSIT,
            'OP03' => Consignment::STATUS_INTRANSIT,
            'OU01' => Consignment::STATUS_INTRANSIT,
            'WE01' => Consignment::STATUS_PROBLEM,
            'WR01' => Consignment::STATUS_RETURNED,
            'WR10' => Consignment::STATUS_RECEIVED,
            'WR11' => Consignment::STATUS_RETURNED,
            'WR11' => Consignment::STATUS_RETURNED,
            'WR12' => Consignment::STATUS_RETURNED,
            'WR13' => Consignment::STATUS_RETURNED,
            'WR14' => Consignment::STATUS_RETURNED,
            'WR15' => Consignment::STATUS_RETURNED,
            'WR16' => Consignment::STATUS_RETURNED,
            'WR17' => Consignment::STATUS_RETURNED,
            'WR21' => Consignment::STATUS_RELABLED
    );

    public static $oneworld_daipost = array(
        
            'CI02' => 126,
            'DA01' => 137,
            'DC01' => 117,
            'DC02' => 117,
            'DC11' => 120,
            'DC25' => 135,
            'DC29' => 135,
            'DC30' => 115,
            'DE03' => 119,
            'DE10' => 115,
            'DE20' => 138,
            'DH01' => 137,
            'FD01' => 121,
            'FD02' => 121,
            'FD03' => 121,
            'FD10' => 137,
            'FD11' => 137,
            'FD21' => 137,
            'FD22' => 137,
            'FD31' => 121,
            'FE01' => 138,
            'FE03' => 138,
            'FE04' => 138,
            'FE07' => 135,
            'FE10' => 138,
            'FE11' => 138,
            'FI01' => 144,
            'FP01' => 137,
            'FP03' => 137,
            'FP05' => 111,
            'FP07' => 137,
            'OP01' => 137,
            'OP02' => 137,
            'OP03' => 137,
            'OU01' => 137,
            'WE01' => 115,
            'WR01' => 137,
            'WR10' => 137,
            'WR11' => 138,
            'WR11' => 138,
            'WR12' => 138,
            'WR13' => 138,
            'WR14' => 138,
            'WR15' => 138,
            'WR16' => 138,
            'WR17' => 138,
            'WR21' => 138   
    );
    public static function getOweStatusCode($daipostStatus){
        $daipostTrackingStatus = self::$oneworld_daipost;
        return isset($daipostTrackingStatus[$daipostStatus]) ? $daipostTrackingStatus[$daipostStatus] : 0;
    }
    public static function getConsignmentStatus($daipostStatus){
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$daipostStatus]) ? $consignmentStatusCodes[$daipostStatus] : 0;
    }
}
