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
class UPSTrackingStatus {
    
    public function __construct() {
        
    }
    
    public static $ups_status_code = array(
        'MP' => "Order Processed: Ready for UPS",
        'XD' => "Drop-Off",
        'ZO' => "The UPS Access Point™ location has prepared the package for return to UPS or pickup by UPS.",
        'OR' => "Origin Scan",
        'DP' => "Departure Scan",
        'AR' => "Arrival Scan",
        '39' => "As requested by the sender, the package is being held for pickup at a UPS location.",
        'KB' => "Delivered",
        'DS' => "Destination Scan",
        'DH' =>	"Delivery Will Be Delayed By One Business Day.",
        'PU' =>	"Pickup Scan",
        'OF' => "Out For Delivery"
    );
 
    public static $consignment_status_code = array(
        'MP' => Consignment::STATUS_LABEL_CREATED,
        'XD' => Consignment::STATUS_INTRANSIT,
        'ZO' => Consignment::STATUS_INTRANSIT,
        'OR' => Consignment::STATUS_INTRANSIT,
        'DP' => Consignment::STATUS_INTRANSIT,
        'AR' => Consignment::STATUS_INTRANSIT,
        '39' => Consignment::STATUS_INTRANSIT,
        'KB' => Consignment::STATUS_DELIVERED,
        'DS' => Consignment::STATUS_INTRANSIT,
        'DH' =>	Consignment::STATUS_INTRANSIT,
        'PU' =>	Consignment::STATUS_INTRANSIT,
        'OF' => Consignment::STATUS_INTRANSIT
    );

    public static $oneworld_ups = array(
        'MP' => 144,
        'XD' => 159,
        'ZO' => 137,
        'OR' => 137,
        'DP' => 137,
        'AR' => 137,
        '39' => 137,
        'KB' => 121,
        'DS' => 137,
        'DH' =>	120,
        'PU' =>	137,
        'OF' => 111
    );
    public static function getOweStatusCode($upsStatus){
        $upsTrackingStatus = self::$oneworld_ups;
        return isset($upsTrackingStatus[$upsStatus]) ? $upsTrackingStatus[$upsStatus] : 0;
    }
    public static function getConsignmentStatus($upsStatus){
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$upsStatus]) ? $consignmentStatusCodes[$upsStatus] : 0;
    }
}
