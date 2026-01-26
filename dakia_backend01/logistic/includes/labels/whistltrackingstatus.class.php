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
class WhistlTrackingStatus {
    
    public function __construct() {
        
    }
    
    public static $whistl_status_code = array( 
            'Scan collected by receipt depot.'      => 'Scan collected by receipt depot.',
            'Scanned at Dispatch Hub.'              => 'Scanned at Dispatch Hub.',
            'Item handend over to Postal Operator.' => 'Item handend over to Postal Operator.',
            'Item Delivered.'                       => 'Item Delivered.',
            'Arrange Redelivery for Item.'          => 'Arrange Redelivery for Item.',
            'Item Undeliverable and Retrun.'        => 'Item Undeliverable and Retrun.'            
    );
 
    public static $consignment_status_code = array(
            'Scan collected by receipt depot.'      => Consignment::STATUS_RECEIVED,// carrier recived 
            'Scanned at Dispatch Hub.'              => Consignment::STATUS_INTRANSIT,  
            'Item handend over to Postal Operator.' => Consignment::STATUS_INTRANSIT,  
            'Item Delivered.'                       => Consignment::STATUS_DELIVERED,// delivred
            'Arrange Redelivery for Item.'          => Consignment::STATUS_INTRANSIT,// in transit 
            'Item Undeliverable and Retrun.'        => Consignment::STATUS_RETURNED  
    );

    public static $oneworld_whistl = array(        
            'Scan collected by receipt depot.'      => 148,
            'Scanned at Dispatch Hub.'              => 137,
            'Item handend over to Postal Operator.' => 137,
            'Item Delivered.'                       => 121,
            'Arrange Redelivery for Item.'          => 137,
            'Item Undeliverable and Retrun.'        => 138
    );
    public static function getOweStatusCode($whistlStatus){
        $whistlTrackingStatus = self::$oneworld_whistl;
        return isset($whistlTrackingStatus[$whistlStatus]) ? $whistlTrackingStatus[$whistlStatus] : 0;
    }
    public static function getConsignmentStatus($whistlStatus){
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$whistlStatus]) ? $consignmentStatusCodes[$whistlStatus] : 0;
    }
}
