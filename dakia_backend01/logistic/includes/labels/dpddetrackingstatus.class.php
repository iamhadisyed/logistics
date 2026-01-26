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
class DPDDETrackingStatus {

    public function __construct() {

    }

    public static $dpdde_status_code = array(
        "Parcel handed to DPD" => "Parcel handed to DPD",
        "In transit" => "In transit",
        "At parcel delivery centre" => "At parcel delivery centre",
        "Parcel out for delivery" => "Parcel out for delivery",
        "Parcel delivered" => "Parcel delivered"
    );

    public static $consignment_status_code = array(
        "Parcel handed to DPD" => Consignment::STATUS_INTRANSIT,
        "In transit" => Consignment::STATUS_INTRANSIT,
        "At parcel delivery centre" => Consignment::STATUS_INTRANSIT,
        "Parcel out for delivery" => Consignment::STATUS_INTRANSIT,
        "Parcel delivered" => Consignment::STATUS_DELIVERED,
    );

    public static $oneworld_dpdde = array(
        "Parcel handed to DPD" => 137,
        "In transit" => 137,
        "At parcel delivery centre" => 137,
        "Parcel out for delivery" => 111,
        "Parcel delivered" => 121
    );

    public static function getOweStatusCode($dpddeStatus){
        $dpddeTrackingStatus = self::$oneworld_dpdde;
        return isset($dpddeTrackingStatus[$dpddeStatus]) ? $dpddeTrackingStatus[$dpddeStatus] : 0;
    }
    public static function getConsignmentStatus($dpddeStatus){
        $consignmentStatusCodes = Tracking::$oneworld_consignment_code_mapping;
        return isset($consignmentStatusCodes[$dpddeStatus]) ? $consignmentStatusCodes[$dpddeStatus] : 0;
    }
}
