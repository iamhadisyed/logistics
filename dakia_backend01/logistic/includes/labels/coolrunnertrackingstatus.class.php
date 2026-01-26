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
class CoolRunnerTrackingStatus {
    
    public function __construct() {
        
    }
    
    public static $coolrunner_status_code = array( 
        "Consignment created" => "Consignment created",
        "Shipped from terminal" => "Shipped from terminal",
        "Received at terminal" => "Received at terminal",
        "Receiver notified" => "Receiver notified",
        "Picked up at DHL SERVICE POINT" => "Picked up at DHL SERVICE POINT"
        );

    public static $consignment_status_code = array(
        "Consignment created" => Consignment::STATUS_LABEL_CREATED,
        "Shipped from terminal" => Consignment::STATUS_DISPATCHED,
        "Received at terminal" => Consignment::STATUS_RECEIVED,
        "Receiver notified" => Consignment::STATUS_INTRANSIT,
        "Picked up at DHL SERVICE POINT" => Consignment::STATUS_DELIVERED
        );

    public static $oneworld_coolrunner = array(
        "Consignment created" => 144,
        "Shipped from terminal" => 126,
        "Received at terminal" => 148,
        "Receiver notified" => 137,
        "Picked up at DHL SERVICE POINT" => 121
    );
    public static function getOweStatusCode($coolRunnerStatus){
        $coolRunnerTrackingStatus = self::$oneworld_coolrunner;
        return isset($coolRunnerTrackingStatus[$coolRunnerStatus]) ? $coolRunnerTrackingStatus[$coolRunnerStatus] : 0;
    }
    public static function getConsignmentStatus($coolRunnerStatus){
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$coolRunnerStatus]) ? $consignmentStatusCodes[$coolRunnerStatus] : 0;
    }
}
